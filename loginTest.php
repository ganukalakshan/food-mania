<?php
// Ensure the path to autoload.php is correct
require 'vendor/autoload.php';

use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\WebDriverBy;

class LoginTest {
    private $driver;

    public function setUp() {
        // Set up Chrome options
        $options = new ChromeOptions();
        $options->addArguments(["--headless", "--disable-gpu"]); // Optional: Run headless if needed

        // Specify the path to ChromeDriver if not in PATH
        $driverPath = '/path/to/chromedriver'; // Update this path if necessary

        // Set up ChromeDriver
        $this->driver = RemoteWebDriver::create(
            'http://localhost:4444/wd/hub', // URL of the Selenium server
            DesiredCapabilities::chrome()->setCapability(ChromeOptions::CAPABILITY, $options)
        );
        $this->driver->manage()->window()->setSize(new Facebook\WebDriver\WebDriverDimension(782, 871));
    }

    public function tearDown() {
        $this->driver->quit();
    }

    public function testLogin() {
        $this->driver->get('http://localhost/web/userlogin.php');
        
        // Test valid credentials
        $this->driver->findElement(WebDriverBy::name('username'))->sendKeys('user');
        $this->driver->findElement(WebDriverBy::name('password'))->sendKeys('1010');
        $this->driver->findElement(WebDriverBy::cssSelector('input[type="submit"]'))->click();
        
        // Check if redirected to main.php
        $currentUrl = $this->driver->getCurrentURL();
        assert($currentUrl == 'http://localhost/web/main.php', 'Login with valid credentials failed');

        // Test invalid credentials
        $this->driver->get('http://localhost/web/userlogin.php');
        $this->driver->findElement(WebDriverBy::name('username'))->sendKeys('invaliduser');
        $this->driver->findElement(WebDriverBy::name('password'))->sendKeys('wrongpassword');
        $this->driver->findElement(WebDriverBy::cssSelector('input[type="submit"]'))->click();
        
        // Check if error message is displayed
        $errorMessage = $this->driver->findElement(WebDriverBy::cssSelector('.error'))->getText();
        assert($errorMessage == 'Invalid username or password!', 'Error message not displayed for invalid credentials');
    }
}

// Run the test
$test = new LoginTest();
$test->setUp();
$test->testLogin();
$test->tearDown();
