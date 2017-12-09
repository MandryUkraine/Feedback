<?php
/**
 * Перегляд
 *
 * @author      Артем Висоцький <a.vysotsky@gmail.com>
 * @package     Mandry\Feedback
 * @link        https://відгуки.мандри.укр
 * @copyright   Всі права застережено (c) 2017 Мандри
 */

namespace Mandry\Feedback;

class View extends \SimpleXMLElement {

    /**
     * Форматує відлагодження
     */
    protected function formatDebug() {

        $this->attributes()->timestamp = time();

        $time = 0;

        if (isset($this->debug->mapper->queries)) {

            foreach($this->debug->mapper->queries->query as $key => $query) {

                $sql = $query->attributes()->sql;

                $sql = preg_replace("/\t/", "", $sql);

                $sql = preg_replace("/\t/", "", $sql);

                $sql = preg_replace("/\n\s{12}/", "\n", $sql);

                $sql = preg_replace("/\n[\s]*\n/", "\n", $sql);

                $sql = preg_replace("/^\n(.*)\n$/iu", "$1", $sql);

                $sql = preg_replace("/(|\s)([A-Z_]+)(\(|\s)/", "$1<span>$2</span>$3", $sql);

                $query->attributes()->sql = $sql;

                $timeQuery = (float) str_replace(',', '.', $query->attributes()->time);

                $time += $timeQuery;

                $timeQuery = sprintf('%01.5f', $timeQuery);

                $query->attributes()->time = $timeQuery;
            }

            $this->debug->mapper->addAttribute('time', sprintf('%01.3f', $time));
        }

        $this->debug->attributes()->time = microtime(true) - $this->debug->attributes()->time;

        $this->debug->attributes()->time = sprintf('%01.3f', $this->debug->attributes()->time);

        $this->debug->attributes()->memory = (memory_get_usage() - $this->debug->attributes()->memory) / 1024;

        $this->debug->attributes()->memory = sprintf('%01.3f', $this->debug->attributes()->memory);

        $this->debug->attributes()->memoryPeak = sprintf('%01.3f', (memory_get_peak_usage() / 1024));
    }

    /**
     * Повертає html
     *
     * return string HTML-код сторінки
     */
    public function getHTML() {

        if (defined('_DEBUG') && _DEBUG) $this->formatDebug();

        $xml = new \DOMDocument('1.0', 'UTF-8');

        $xml->loadXML($this->asXML());

        $xslt = new \XSLTProcessor();

        $xsl = new \DOMDocument('1.0', 'UTF-8');

        $xslFile = _PATH_PRIVATE . '/' . ((string) $this->attributes()->xsl);

        $xsl->load($xslFile, LIBXML_NOCDATA);

        $xslt->importStylesheet($xsl);

        return $xslt->transformToXML($xml);
    }
}