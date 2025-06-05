<?php

// DET URL Whitelist controls the URLs that can be used in Data Entry Triggers (DET) in REDCap.
// This module blocks the testing of DET URLs not in the whitelist and prevents saving of DET URLs in project settings if they are not whitelisted.
// This module requires the config.json to contain following properties:
// - "enable-every-page-hooks-on-system-pages": true
// - "framework-version": 16

namespace DetUrlWhitelist\DETURLWhitelist;

use \RCView;

class DETURLWhitelist extends \ExternalModules\AbstractExternalModule
{
    public function redcap_every_page_before_render($project_id)
    {

        // block testing of DET URLs not in whitelist send via ajax request
        $script = basename($_SERVER['SCRIPT_NAME']);
        if ($script === 'test_http_request.php' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $url = $_POST['url'];
            if (isset($url) && $url != "" && !$this->isUrlWhitelisted($url)) {
                $_POST['url'] = "";
                // return '0' to indicate that the URL cannot be called
                print '0';
                // stop the execution of the script to prevent outgoing HTTP request
                $this->framework->exitAfterHook();
            };
        }

        // block saving of DET URL in project settings if not in whitelist
        if (PAGE === 'ProjectGeneral/edit_project_settings.php' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            // the url is sent via POST request with the key 'data_entry_trigger_url'
            $url = $_POST['data_entry_trigger_url'];
            if (isset($url) && $url != "" && !$this->isUrlWhitelisted($url)) {
                // empty the URL to prevent saving it in the project settings
                $_POST['data_entry_trigger_url'] = "";

                // stop the execution of the script that also prevents saving the project settings
                $this->framework->exitAfterHook();

                // display error message and redirect to project setup page
                $errorBanner = RCView::div([
                    'style' => 'margin:20px; border: 1px solid red; color:red; padding: 12px; line-height: 1.3;'
                ], 
                    RCView::b("Error: ") . "The URL you entered is not allowed in the whitelist of the external module <b>DET URL Whitelist Blocker</b>. No changes were saved.<br>Please contact your system administrator to add the URL to the whitelist in the system module settings."
                );

                // add a link to return to the project setup page
                $redirectURL = APP_PATH_WEBROOT . "ProjectSetup/index.php?pid=$project_id";
                $redirectLink = RCView::a([
                    'href' => $redirectURL,
                    'style' => 'margin: 0px 20px;',
                ], "Return to Project Setup");

                echo <<<HTML
                <html>
                <head>
                    <title>Error</title>
                </head>
                <body>
                HTML;

                print $errorBanner;
                print $redirectLink;

                echo <<<HTML
                </body>
                </html>
                HTML;
            };
        }
    }

    // Check if the URL is available in the whitelist
    public function isUrlWhitelisted($url)
    {
        $whitelist = $this->getSystemSetting('allowed_det_urls');
        foreach ($whitelist as $allowed) {
            if ($url === $allowed) return true;
        }
        return false;
    }
}
