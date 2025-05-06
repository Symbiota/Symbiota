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
        global $CLIENT_ROOT, $SERVER_ROOT, $SERVER_HOST;

        $baseUrl = "{$this->protocol}://{$SERVER_HOST}" . $CLIENT_ROOT;

        $conn = MySQLiConnectionFactory::getCon("readonly");

        if (!$conn) {
            $this->sitemapMessage = "Failed to connect to the database.";
            return false;
        }

        $xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
        $xml .= "<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n";

        $xml .= $this->generateCollectionsSitemap($conn, $baseUrl);
        $xml .= $this->generateChecklistsSitemap($baseUrl);
        $xml .= $this->generateProjectsSitemap($baseUrl);
        $xml .= $this->generateExsiccataSitemap($conn, $baseUrl);
        $xml .= $this->generateTaxaSitemap($conn, $baseUrl);

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
                LEFT JOIN omcollectionstats s ON c.collid = s.collid";
        $rs = $conn->query($sql);
        $xml = '';
        while ($row = $rs->fetch_assoc()) {
            $xml .= "  <url>\n";
            $xml .= "    <loc>{$baseUrl}/collections/misc/collprofiles.php?collid={$row['collid']}</loc>\n";

            $timestamp = !empty($row['datelastmodified']) ? $row['datelastmodified'] : $row['initialtimestamp'];
            if (!empty($timestamp)) {
                $xml .= "    <lastmod>" . date("Y-m-d", strtotime($timestamp)) . "</lastmod>\n";
            }
            $xml .= "  </url>\n";
        }
        return $xml;
    }

    private function generateChecklistsSitemap($baseUrl) {
        return "  <url>\n" .
               "    <loc>{$baseUrl}/checklists/index.php</loc>\n" .
               "  </url>\n";
    }

    private function generateProjectsSitemap($baseUrl) {
        return "  <url>\n" .
               "    <loc>{$baseUrl}/projects/index.php</loc>\n" .
               "  </url>\n";
    }

    private function generateExsiccataSitemap($conn, $baseUrl) {
        $xml = '';
        $sql = "SELECT ometid, initialtimestamp FROM omexsiccatititles";
        $rs = $conn->query($sql);
        while ($row = $rs->fetch_assoc()) {
            $xml .= "  <url>\n";
            $xml .= "    <loc>{$baseUrl}/collections/exsiccati/index.php?ometid={$row['ometid']}</loc>\n";
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
            $xml .= "  <url>\n";
            $xml .= "    <loc>{$baseUrl}/taxa/index.php?tid={$row['tid']}</loc>\n";
            $timestamp = !empty($row['modifiedtimestamp']) ? $row['modifiedtimestamp'] : $row['initialtimestamp'];

            if (!empty($timestamp))
                $xml .= "    <lastmod>" . date("Y-m-d", strtotime($timestamp)) . "</lastmod>\n";
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
}

