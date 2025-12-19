<?php

namespace Tests;

use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Laravel\Dusk\TestCase as BaseTestCase;

abstract class DuskTestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * Selenium コンテナを使うので、ローカル chromedriver(9515) は起動しない
     *
     * @beforeClass
     * @return void
     */
    public static function prepare(): void
    {
        // 何もしない（static::startChromeDriver(); は呼ばない）
    }

    /**
     * Create the RemoteWebDriver instance.
     */
    protected function driver(): RemoteWebDriver
    {
        $args = [
            '--window-size=1920,1080',
            '--no-sandbox',
            '--disable-dev-shm-usage',
            '--disable-gpu',
        ];

        // headless を無効化したい場合（php artisan dusk --env=... 等で制御）
        if (!$this->hasHeadlessDisabled()) {
            // selenium/standalone-chrome では headless=new が使えない場合があるので通常 headless で安定
            $args[] = '--headless';
        }

        if ($this->shouldStartMaximized()) {
            $args[] = '--start-maximized';
        }

        $options = (new ChromeOptions())->addArguments($args);

        return RemoteWebDriver::create(
            env('DUSK_DRIVER_URL', 'http://selenium:4444/wd/hub'),
            DesiredCapabilities::chrome()->setCapability(ChromeOptions::CAPABILITY, $options)
        );
    }

    /**
     * Determine whether the Dusk command has disabled headless mode.
     */
    protected function hasHeadlessDisabled(): bool
    {
        return isset($_SERVER['DUSK_HEADLESS_DISABLED']) ||
            isset($_ENV['DUSK_HEADLESS_DISABLED']);
    }

    /**
     * Determine if the browser window should start maximized.
     */
    protected function shouldStartMaximized(): bool
    {
        return isset($_SERVER['DUSK_START_MAXIMIZED']) ||
            isset($_ENV['DUSK_START_MAXIMIZED']);
    }
}
