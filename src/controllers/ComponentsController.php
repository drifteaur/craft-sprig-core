<?php
/**
 * @copyright Copyright (c) PutYourLightsOn
 */

namespace putyourlightson\sprig\controllers;

use Craft;
use craft\base\Element;
use craft\elements\User;
use craft\events\ModelEvent;
use craft\helpers\ArrayHelper;
use craft\web\Controller;
use craft\web\UrlRule;
use putyourlightson\sprig\Sprig;
use yii\base\Event;
use yii\web\ForbiddenHttpException;
use yii\web\Response;

class ComponentsController extends Controller
{
    /**
     * @inheritdoc
     */
    protected int|bool|array $allowAnonymous = true;

    /**
     * @inheritdoc
     */
    public function beforeAction($action): bool
    {
        if ($this->request->getIsCpRequest() && Craft::$app->getUser()->getIdentity()->can('accessCp') === false) {
            throw new ForbiddenHttpException();
        }

        return parent::beforeAction($action);
    }

    /**
     * Renders a component.
     */
    public function actionRender(): Response
    {
        $siteId = Sprig::$core->requests->getValidatedParam('sprig:siteId');
        Craft::$app->getSites()->setCurrentSite($siteId);

        $component = Sprig::$core->requests->getValidatedParam('sprig:component');
        $action = Sprig::$core->requests->getValidatedParam('sprig:action');

        $variables = ArrayHelper::merge(
            Sprig::$core->requests->getValidatedParamValues('sprig:variables'),
            Sprig::$core->requests->getVariables()
        );

        $content = '';

        if ($component) {
            $componentObject = Sprig::$core->components->createObject($component, $variables);

            if ($componentObject) {
                if ($action && method_exists($componentObject, $action)) {
                    call_user_func([$componentObject, $action]);
                }

                $content = $componentObject->render();
            }
        } else {
            if ($action) {
                $actionVariables = $this->_runActionInternal($action);
                $variables = ArrayHelper::merge($variables, $actionVariables);
            }

            $template = Sprig::$core->requests->getRequiredValidatedParam('sprig:template');
            $content = Craft::$app->getView()->renderTemplate($template, $variables);
        }

        $response = Craft::$app->getResponse();
        $response->statusCode = 200;
        $response->format = Response::FORMAT_HTML;
        $response->data = Sprig::$core->components->parse($content);

        $cacheDuration = Sprig::$core->requests->getCacheDuration();
        if ($cacheDuration > 0) {
            $response->headers->set('Cache-Control', 'private, max-age=' . $cacheDuration);
        }

        return $response;
    }

    /**
     * Runs an action and returns variables from the response.
     */
    private function _runActionInternal(string $action): array
    {
        // Use a request that accepts JSON, so we can get all the data back.
        // https://github.com/putyourlightson/craft-sprig/issues/301
        Craft::$app->getRequest()->getHeaders()->set('Accept', 'application/json');

        if ($action == 'users/save-user') {
            $this->_registerSaveCurrentUserEvent();
        }

        $actionResponse = Craft::$app->runAction($action);

        if ($actionResponse->getIsOk()) {
            $variables = $actionResponse->data;
        } else {
            $variables = ['errors' => $actionResponse->data];
        }

        $variables['success'] = $actionResponse->getIsOk();

        /**
         * Merge and unset any variable called `variables`
         * https://github.com/putyourlightson/craft-sprig/issues/94#issuecomment-771489394
         *
         * @see UrlRule::parseRequest()
         */
        if (isset($variables['variables'])) {
            $variables = ArrayHelper::merge($variables, $variables['variables']);
            unset($variables['variables']);
        }

        // Override the `currentUser` global variable with a fresh version, in case it was just updated
        // https://github.com/putyourlightson/craft-sprig/issues/81#issuecomment-758619306
        $variables['currentUser'] = Craft::$app->getUser()->getIdentity();

        return $variables;
    }

    /**
     * Registers an event when saving the current user
     */
    private function _registerSaveCurrentUserEvent(): void
    {
        $currentUserId = Craft::$app->getUser()->getId();
        $userId = Craft::$app->getRequest()->getBodyParam('userId');

        if (!$currentUserId || $currentUserId != $userId) {
            return;
        }

        Event::on(User::class, Element::EVENT_AFTER_SAVE,
            function(ModelEvent $event) {
                /** @var User $user */
                $user = $event->sender;

                // Update the user identity and regenerate the CSRF token in case the password was changed
                // https://github.com/putyourlightson/craft-sprig/issues/136
                Craft::$app->getUser()->setIdentity($user);
                Craft::$app->getRequest()->regenCsrfToken();
            }
        );
    }
}
