# Sitemap Checker

A command-line tool to validate URLs in an XML sitemap. This tool downloads an XML sitemap from a specified URL, parses it, and checks each URL to ensure it returns a 200 HTTP status code. It’s useful for SEO and website maintenance to verify that all pages in your sitemap are accessible.

## Installation

To set up the Sitemap Checker on your system, follow these steps:

1. **Clone the repository**:
```bash
git clone https://github.com/RomanNikolyuk/sitemap-checker.git
```
2. **Install dependencies using Composer:**
```bash
composer install --no-dev
```
This will download all required PHP dependencies, including Symfony Console and GuzzleHttp.

## Usage
Run the tool from the command line by providing the URL of the sitemap you want to validate. The script must be executed with PHP and requires a sitemap URL as an argument.
### Command Syntax
```bash
php index.php <sitemap-url> <error-output.txt>
```

## Example
To check a sitemap located at https://example.com/sitemap.xml, run:
```bash
php sitemap-checker.php https://example.com/sitemap.xml output.txt
```
**Note**: The sitemap URL is required. If you don’t provide a URL, the tool may fail with an error due to the way it’s implemented.

## Output
The tool provides feedback as it checks each URL in the sitemap:
* For URLs with issues: If a URL does not return a 200 status code, an error message is displayed with the status code and the URL. For example:
```text
[404] https://example.com/bad-page
```
* Completion message: After checking all URLs, the tool outputs:
```text
[OK]
```
This indicates that the process has finished, even if some URLs failed (those failures are reported individually above).

## Requirements
To run the Sitemap Checker, ensure your system meets these prerequisites:
* PHP 8.2 or higher
* Composer: Required to install PHP dependencies.
* Internet connection: Needed to download the sitemap and check the URLs.

## Additional Notes
* The tool processes sitemap URLs efficiently using XMLReader and makes HTTP requests with GuzzleHttp.
* It supports standard `<loc>` elements in sitemaps and may also handle `<xhtml:link>` elements (e.g., for alternate language versions), depending on the implementation.

If you encounter issues or have suggestions, feel free to contribute or report them via the repository!
