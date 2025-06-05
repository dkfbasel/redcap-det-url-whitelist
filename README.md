# REDCap Data Entry Trigger URL Whitelist

A REDCap External Module that allows system administrators to define a list of URLs that are allowed to be used Data Request Triggers.
This module blocks the testing of DET URLs not in the whitelist and prevents saving of DET URLs in project settings if they are not whitelisted.

## 🔍 Features

- Allow system admin to define a list of allowed URLs for the Data Request Trigger on REDCap instance level
- Prevent sending testing requests of disallowed URLs
- Prevents saving of disallowed URLs

## 🛠️ Installation

1. Clone or download the repository into your REDCap `modules` directory:
   ```bash
   cd /redcap/modules
   git clone https://github.com/your-org/data-entry-trigger-url-validator.git data_entry_trigger_url_validator_v1.0
