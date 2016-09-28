<?php
namespace Pantheon\Terminus\Commands\Site;

use Pantheon\Terminus\Commands\TerminusCommand;
use Pantheon\Terminus\Site\SiteAwareInterface;
use Pantheon\Terminus\Site\SiteAwareTrait;
use Terminus\Exceptions\TerminusException;

class ImportCommand extends TerminusCommand implements SiteAwareInterface
{
    use SiteAwareTrait;
    /**
     * Imports a site archive onto a Pantheon site
     *
     * @authorized
     *
     * @name import
     * @alias site:import
     *
     * @option string $site Name of the site to import to
     * @option string $url  URL at which the import archive exists
     * @usage terminus import --site=<site_name> --url=<archive_url>
     *   Imports the file at the archive URL to the site named.
     */
    public function import($sitename, $url)
    {
        $site = $sitename.'.dev';
        list(, $env) = $this->getSiteEnv($site);
        $workflow = $env->import($url);
        try {
            $workflow->wait();
        } catch (\Exception $e) {
            if ($e->getMessage() == "Successfully queued import_site") {
                throw new TerminusException("Site import failed");
            }
            throw $e;
        }
        $this->log()->notice("Imported site onto Pantheon");
    }
}
