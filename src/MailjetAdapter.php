<?php

namespace robuust\mailjet;

use Craft;
use craft\behaviors\EnvAttributeParserBehavior;
use craft\helpers\App;
use craft\mail\transportadapters\BaseTransportAdapter;
use Symfony\Component\Mailer\Bridge\Mailjet\Transport\MailjetApiTransport;

/**
 * MailjetAdapter implements a Mailjet transport adapter into Craft’s mailer.
 *
 * @property mixed $settingsHtml
 */
class MailjetAdapter extends BaseTransportAdapter
{
    /**
     * @inheritdoc
     */
    public static function displayName(): string
    {
        return 'Mailjet';
    }

    /**
     * @var string The API key that should be used
     */
    public string $apiKey = '';

    /**
     * @var string The API secret that should be used
     */
    public string $apiSecret = '';

    /**
     * @var bool Whether to use sandbox mode
     */
    public bool $useSandboxMode = false;

    /**
     * @inheritdoc
     */
    public function behaviors(): array
    {
        $behaviors = parent::behaviors();
        $behaviors['parser'] = [
            'class' => EnvAttributeParserBehavior::class,
            'attributes' => [
                'apiKey',
                'apiSecret',
                'useSandboxMode',
            ],
        ];
        return $behaviors;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'apiKey' => Craft::t('mailjet-adapter', 'API Key'),
            'apiSecret' => Craft::t('mailjet-adapter', 'API Secret'),
            'useSandboxMode' => Craft::t('mailjet-adapter', 'Use Sandbox Mode'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function defineRules(): array
    {
        return [
            [['apiKey', 'apiSecret'], 'required'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function getSettingsHtml(): ?string
    {
        return Craft::$app->getView()->renderTemplate('mailjet-adapter/settings', [
            'adapter' => $this,
        ]);
    }

    /**
     * @inheritdoc
     */
    public function defineTransport(): array|\Symfony\Component\Mailer\Transport\AbstractTransport
    {
        return new MailjetApiTransport(App::parseEnv($this->apiKey), App::parseEnv($this->apiSecret), sandbox: App::parseBooleanEnv($this->useSandboxMode));
    }
}
