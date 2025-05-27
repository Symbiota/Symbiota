<?php
include_once($SERVER_ROOT.'/classes/Manager.php');

class SitemapXMLManager extends Manager {
    private $host;
    private $database;
    private $port;
    private $username;
    private $protocol = 'https';
    private $sitemapMessage = '';

    public function __construct() {

        $this->host = MySQLiConnectionFactory::$SERVERS[0]['host'];
        $this->database = MySQLiConnectionFactory::$SERVERS[0]['database'];
        $this->port = MySQLiConnectionFactory::$SERVERS[0]['port'];
    }

    public function __destruct() {
        parent::__destruct();
    }

    public function generateSitemap() {
        global $CLIENT_ROOT, $SERVER_ROOT, $SERVER_HOST, $PRIVATE_VIEWING_ONLY;

        $baseUrl = "{$this->protocol}://{$SERVER_HOST}" . $CLIENT_ROOT;

        $conn = MySQLiConnectionFactory::getCon("readonly");

        if (!$conn) {
            $this->sitemapMessage = "Failed to connect to the database.";
            return false;
        }

        $xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
        $xml .= "<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n";

        //add main landing page
        $landingPagePath = '/index.php';
        if ($this->isPathAllowed($landingPagePath)) {
            $xml .= "  <url>\n";
            $xml .= "    <loc>{$baseUrl}{$landingPagePath}</loc>\n";
            $xml .= "  </url>\n";
        }

        //add pages
        $xml .= $this->generateOccurrencesSitemap($conn, $baseUrl);
        $xml .= $this->generateCollectionsSitemap($conn, $baseUrl);
        $xml .= $this->generateChecklistsSitemap($baseUrl);
        $xml .= $this->generateProjectsSitemap($baseUrl);
        $xml .= $this->generateExsiccataSitemap($conn, $baseUrl);
        $xml .= $this->generateTaxaSitemap($conn, $baseUrl);

        //add the public pages if private turned on
        if ($PRIVATE_VIEWING_ONLY)
            $xml .= $this->generateOverrideSitemap($baseUrl);

        $xml .= "</urlset>\n";

        $conn->close();

        $outputDir = $SERVER_ROOT . '/content/sitemaps';

        if (!is_writable($outputDir)) {
            $this->sitemapMessage = "The log directory (e.g. /content/sitemaps/) is not writable by web user.
                We strongly recommend that you adjust directory permissions as defined within the installation
                before running installation/update scripts.";
            return false;
        }
        $outputFile = "{$outputDir}/sitemap.xml";

        if (!is_dir($outputDir)) {
            if (!mkdir($outputDir, 0777, true)) {
                $this->sitemapMessage = "Failed to create sitemap directory: $outputDir";
                return false;
            }
        }

        if (file_put_contents("{$outputDir}/sitemap.xml", $xml) === false) {
            $this->sitemapMessage = "Failed to write sitemap file.";
            return false;
        }

        return true;
    }

    private function generateCollectionsSitemap($conn, $baseUrl) {
        $sql = "SELECT c.collid, c.initialtimestamp, s.datelastmodified
                FROM omcollections c
                INNER JOIN omcollectionstats s ON c.collid = s.collid";
        $rs = $conn->query($sql);
        $xml = '';
        while ($row = $rs->fetch_assoc()) {
            $path = "/collections/misc/collprofiles.php?collid={$row['collid']}";
            if (!$this->isPathAllowed($path)) continue;

            $xml .= "  <url>\n";
            $xml .= "    <loc>{$baseUrl}{$path}</loc>\n";

            $timestamp = !empty($row['datelastmodified']) ? $row['datelastmodified'] : $row['initialtimestamp'];
            if (!empty($timestamp)) {
                $xml .= "    <lastmod>" . date("Y-m-d", strtotime($timestamp)) . "</lastmod>\n";
            }
            $xml .= "  </url>\n";
        }
        return $xml;
    }

    private function generateChecklistsSitemap($baseUrl) {
        $path = "/checklists/index.php";
        if (!$this->isPathAllowed($path)) return '';

        return "  <url>\n" .
               "    <loc>{$baseUrl}{$path}</loc>\n" .
               "  </url>\n";
    }

    private function generateProjectsSitemap($baseUrl) {
        $path = "/projects/index.php";
        if (!$this->isPathAllowed($path)) return '';

        return "  <url>\n" .
               "    <loc>{$baseUrl}{$path}</loc>\n" .
               "  </url>\n";
    }

    private function generateExsiccataSitemap($conn, $baseUrl) {
        $xml = '';
        $sql = "SELECT ometid, initialtimestamp FROM omexsiccatititles";
        $rs = $conn->query($sql);
        while ($row = $rs->fetch_assoc()) {
            $path = "/collections/exsiccati/index.php?ometid={$row['ometid']}";
            if (!$this->isPathAllowed($path)) continue;

            $xml .= "  <url>\n";
            $xml .= "    <loc>{$baseUrl}{$path}</loc>\n";
            $xml .= "    <lastmod>" . date("Y-m-d", strtotime($row['initialtimestamp'])) . "</lastmod>\n";
            $xml .= "  </url>\n";
        }
        return $xml;
    }

    private function generateTaxaSitemap($conn, $baseUrl) {
        $xml = '';
        $sql = "SELECT tid, modifiedtimestamp, initialtimestamp FROM taxa WHERE rankid <= 180";
        $rs = $conn->query($sql);
        while ($row = $rs->fetch_assoc()) {
            $path = "/taxa/index.php?tid={$row['tid']}";
            if (!$this->isPathAllowed($path)) continue;

            $xml .= "  <url>\n";
            $xml .= "    <loc>{$baseUrl}{$path}</loc>\n";
            $timestamp = !empty($row['modifiedtimestamp']) ? $row['modifiedtimestamp'] : $row['initialtimestamp'];
            if (!empty($timestamp)) {
                $xml .= "    <lastmod>" . date("Y-m-d", strtotime($timestamp)) . "</lastmod>\n";
            }
            $xml .= "  </url>\n";
        }
        return $xml;
    }

    public function setProtocol($protocol) {
        if (in_array($protocol, ['http', 'https'])) {
            $this->protocol = $protocol;
        }
    }

    public function getSitemapMessage() {
        return $this->sitemapMessage;
    }

    private function isPathAllowed($path) {
        global $PRIVATE_VIEWING_ONLY, $PRIVATE_VIEWING_OVERRIDES;
        if (!$PRIVATE_VIEWING_ONLY) return true;
        foreach ($PRIVATE_VIEWING_OVERRIDES as $allowedPath) {
            if (strpos($path, $allowedPath) === 0) {
                return true;
            }
        }
        return false;
    }

    private function generateOverrideSitemap($baseUrl) {
        global $PRIVATE_VIEWING_OVERRIDES;

        $xml = '';
        if (is_array($PRIVATE_VIEWING_OVERRIDES)) {
            foreach ($PRIVATE_VIEWING_OVERRIDES as $path) {

                if ($path === '/index.php' || $path === '/projects/index.php' || $path === '/checklists/index.php') continue;

                $xml .= "  <url>\n";
                $xml .= "    <loc>{$baseUrl}{$path}</loc>\n";
                $xml .= "  </url>\n";
            }
        }
        return $xml;
    }

    private function generateOccurrencesSitemap($conn, $baseUrl) {
        $xml = '';
        $sql = "SELECT occid, datelastmodified FROM omoccurrences ORDER BY occid LIMIT 500";
        $rs = $conn->query($sql);

        if ($rs) {
            while ($row = $rs->fetch_assoc()) {
                $path = "/collections/individual/index.php?occid={$row['occid']}";
                if (!$this->isPathAllowed($path)) continue;

                $xml .= "  <url>\n";
                $xml .= "    <loc>{$baseUrl}{$path}</loc>\n";
                if (!empty($row['datelastmodified'])) {
                    $xml .= "    <lastmod>" . date("Y-m-d", strtotime($row['datelastmodified'])) . "</lastmod>\n";
                }
                $xml .= "  </url>\n";
            }
        }
        return $xml;
    }
}

