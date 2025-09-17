<?php

namespace Toumoro\TmMlLinks\Utility;

use AllowDynamicProperties;
use TYPO3\CMS\Frontend\Typolink\LinkResult;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\PathUtility;


#[AllowDynamicProperties]
class Links
{

    public $buildLink = false;

    /**
     * Main action
     *
     */
    public function main($content, $conf)
    {
        $this->settings = $GLOBALS['TYPO3_REQUEST']->getAttribute('frontend.typoscript')->getSetupArray()['plugin.']['tx_tmmllinks.'];

        $fileType = '';
        if (isset($GLOBALS['TSFE']->register['fileType'])) {
            $fileType = $GLOBALS['TSFE']->register['fileType'];
        }
        $linkType = $GLOBALS['TSFE']->register['linkType'];
        $content = $linkTag = $GLOBALS['TSFE']->register['tag'];
        $url = urldecode($GLOBALS['TSFE']->register['url']);

        // Use given seperator
        $this->separator = isset($this->settings['separator']) ? $this->settings['separator'] : ' ';
        $this->tag = '';
        $proddomain = $this->settings['replaceDomain'];
        // Go through configuration and modify the link
        if (!empty($proddomain)) {

            if (($linkType == 'url') && ((strpos($url, $_SERVER['HTTP_HOST']) > -1) || (strpos($url, $proddomain) > -1))) {
                $linkType = 'file';

                $protocol = stripos($_SERVER['SERVER_PROTOCOL'], 'https') === true ? 'https://' : 'http://';

                $url = str_replace($protocol . $_SERVER['HTTP_HOST'] . '/', '', $url);
                $url = str_replace('https://' . $proddomain . '/', '', $url);



                $url = preg_replace('/\#page=([0-9]+)/', '', $url);
                $fileType = preg_replace('/\#page=([0-9]+)/', '', $fileType);
            }
        }
        switch ($linkType) {
            case 'file':
                if (!empty($this->settings['baseFolder'])) {
                    $url = str_replace($this->settings['baseFolder'], '', $url);
                }
                $this->prepareFileLink($content, $fileType, $linkType, $linkTag, $url);
                break;
            case 'email':
                $this->prepareMailtoLink($content, $fileType, $linkType, $linkTag, $url);
                break;
            case 'page':
                $this->preparePageLink($content, $fileType, $linkType, $linkTag, $url);
                break;
            case 'url':
                $this->prepareUrlLink($content, $fileType, $linkType, $linkTag, $url);
                break;
        }

        // Delete temp variables
        unset($GLOBALS['TSFE']->register['fileType']);
        unset($GLOBALS['TSFE']->register['linkType']);
        unset($GLOBALS['TSFE']->register['tag']);
        unset($GLOBALS['TSFE']->register['url']);

        if (!$this->buildLink) {
            $this->tag = $linkTag;
        }

        return str_replace("&amp;", "&", $this->tag);
    }

    /**
     * Prepares the tag for a link of type "file".
     *
     * @param	string		$content
     * @param	string		$fileType
     * @param	string		$linkType
     * @param	string		$linkTag
     * @param	string		$url
     * @return	void
     */
    protected function prepareFileLink($content, $fileType, $linkType, $linkTag, $url)
    {
        // Check if there is anything defined for this filetype and if the file exists
        if (substr($url, 0, 1) == "/") {
            $url = substr($url, 1);
        }

        $pattern = '/f=([0-9]+)/';
        $matchUid = '';

        if (preg_match($pattern, $url, $matches)) {

            $matchUid = (int)$matches[1];
            $resourceFactory = GeneralUtility::makeInstance(ResourceFactory::class);
            $file = $resourceFactory->getFileObject($matchUid);

            $fileType = $file->getExtension();
        };

        if (isset($this->settings[$fileType . '.']) && $url) {
            $settings = $this->settings[$fileType . '.'];
            ksort($settings);

            foreach ($settings as $data) {

                switch (str_replace('.', '', key($data))) {
                    case 'image':
                        $this->tag .= $this->insertImage($data, $linkTag);
                        break;

                    case 'linkTag':
                        $this->tag .= $this->insertLink($data, $content, $url);
                        break;

                    case 'openingATag':
                        $this->tag .= $this->insertOpeningATag($data, $linkTag, $url);
                        break;

                    case 'linkText':
                        $this->tag .= $this->insertLinkText($data, $content);
                        break;

                    case 'closingATag':
                        $this->tag .= $this->insertClosingATag($data);
                        break;

                    case 'filename':
                        $this->tag .= $this->insertFilename($data, $url);
                        break;

                    case 'revisionDate':
                        $this->tag .= $this->insertRevisionDate($data, $url);
                        break;

                    case 'filesize':
                        $this->tag .= $this->insertFilesize($data, $url, $linkTag);
                        break;

                    case 'dimensions':
                        $this->tag .= $this->insertDimensions($data, $url);
                        break;

                    case 'string':
                        $this->tag .= $this->insertString($data, $linkTag);
                        break;
                }
            }
        }

        // Check if there are any default settings and if the file exists
        elseif (isset($this->settings['default.']) && file_exists($url)) {
            $settings = $this->settings['default.'];
            ksort($settings);

            foreach ($settings as $data) {
                switch (key($data)) {
                    case 'image':
                        $this->tag .= $this->insertImage($data, $linkTag);
                        break;

                    case 'linkTag':
                        $this->tag .= $this->insertLink($data, $content, $url);
                        break;

                    case 'openingATag':
                        $this->tag .= $this->insertOpeningATag($data, $linkTag, $url);
                        break;

                    case 'linkText':
                        $this->tag .= $this->insertLinkText($data, $content);
                        break;

                    case 'closingATag':
                        $this->tag .= $this->insertClosingATag($data);
                        break;

                    case 'filename':
                        $this->tag .= $this->insertFilename($data, $url);
                        break;

                    case 'revisionDate':
                        $this->tag .= $this->insertRevisionDate($data, $url);
                        break;

                    case 'filesize':
                        $this->tag .= $this->insertFilesize($data, $url, $linkTag);
                        break;

                    case 'string':
                        $this->tag .= $this->insertString($data, $linkTag);
                        break;
                }
            }
        }

        // Check if there are any settings if the file doesn't exist
        elseif (!file_exists($url) && isset($this->settings['notFound.'])) {
            $settings = $this->settings['notFound.'];
            ksort($settings);

            foreach ($settings as $data) {
                switch (key($data)) {
                    case 'image':
                        $this->tag .= $this->insertImage($data, $linkTag);
                        break;

                    case 'string':
                        $this->tag .= $this->insertString($data, $linkTag);
                        break;

                    case 'linkText':
                        $this->tag .= $this->insertLinkText($data, $content);
                        break;

                    case 'linkTag':
                        $this->tag .= $this->insertLink($data, $content, $url);
                        break;
                }
            }
        }
    }

    /**
     * Prepares the tag for a link of type "mailto".
     *
     * @param	string		$content
     * @param	string		$fileType
     * @param	string		$linkType
     * @param	string		$linkTag
     * @param	string		$url
     * @return	void
     */
    protected function prepareMailtoLink($content, $fileType, $linkType, $linkTag, $url)
    {
        if (isset($this->settings['mailto.'])) {
            $settings = $this->settings['mailto.'];
            ksort($settings);

            foreach ($settings as $data) {
                switch (key($data)) {
                    case 'image':
                        $this->tag .= $this->insertImage($data, $linkTag);
                        break;

                    case 'linkTag':
                        $this->tag .= $this->insertLink($data, $content, $url);
                        break;

                    case 'openingATag':
                        $this->tag .= $this->insertOpeningATag($data, $linkTag, $url);
                        break;

                    case 'linkText':
                        $this->tag .= $this->insertLinkText($data, $content);
                        break;

                    case 'closingATag':
                        $this->tag .= $this->insertClosingATag($data);
                        break;

                    case 'string':
                        $this->tag .= $this->insertString($data, $linkTag);
                        break;
                }
            }
        }
    }

    /**
     * Prepares the tag for a link of type "page".
     *
     * @param	string		$content
     * @param	string		$fileType
     * @param	string		$linkType
     * @param	string		$linkTag
     * @param	string		$url
     * @return	void
     */
    protected function preparePageLink($content, $fileType, $linkType, $linkTag, $url)
    {
        if (isset($this->settings['internal.'])) {
            $settings = $this->settings['internal.'];
            ksort($settings);

            foreach ($settings as $data) {
                switch (key($data)) {
                    case 'image':
                        $this->tag .= $this->insertImage($data, $linkTag);
                        break;

                    case 'linkTag':
                        $this->tag .= $this->insertLink($data, $content, $url);
                        break;

                    case 'openingATag':
                        $this->tag .= $this->insertOpeningATag($data, $linkTag, $url);
                        break;

                    case 'linkText':
                        $this->tag .= $this->insertLinkText($data, $content);
                        break;

                    case 'closingATag':
                        $this->tag .= $this->insertClosingATag($data);
                        break;

                    case 'string':
                        $this->tag .= $this->insertString($data, $linkTag);
                        break;
                }
            }
        }
    }

    /**
     * Prepares the tag for a link of type "url".
     *
     * @param	string		$content
     * @param	string		$fileType
     * @param	string		$linkType
     * @param	string		$linkTag
     * @param	string		$url
     * @return	void
     */
    protected function prepareUrlLink($content, $fileType, $linkType, $linkTag, $url)
    {
        $settings = array();

        if (isset($this->settings['externalDomain.'])) {
            $domains = $this->settings['externalDomain.'];

            if (count($domains)) {
                foreach ($domains as $settings) {
                    if (!isset($settings['domain'])) {
                        continue;
                    }
                    $domain = $settings['domain'];
                    unset($settings['domain']);

                    if (substr($url, 0, strlen($domain)) != $domain) {
                        $settings = array();
                    } else {
                        break;
                    }
                }
            }
        }

        if (isset($this->settings['external.']) && count($settings) == 0) {
            $settings = $this->settings['external.'];
        }

        if (count($settings)) {
            ksort($settings);
            foreach ($settings as $data) {
                switch (key($data)) {
                    case 'image':
                        if (!empty($this->tag)) {
                            $this->tag .= $this->separator;
                        }

                        $ext = FALSE;
                        // Get filetype
                        $file = basename($url);
                        if (preg_match('/(.*)\.([^\.]*$)/', $file, $reg)) {
                            $ext = strtolower($reg[2]);
                            $ext = ($ext === 'jpeg') ? 'jpg' : $ext;
                        }

                        // Add image
                        if (($ext) && (isset($data['image.'][$ext]))) {
                            $image = $data['image.'][$ext];
                            $alt = isset($data['image.'][$ext]['alt']) ? $data['image.'][$ext]['alt'] : '';
                        } else {
                            $image = $data['image'];
                            $alt = isset($data['image.']['alt']) ? $data['image.']['alt'] : '';
                        }
                        $image = $this->resolveImageName($image);
                        $imageTag = '<img src="' . $image . '" alt="' . $alt . '"/>';

                        // Add link if configured
                        if (isset($data['image.']['link']) && $data['image.']['link'] == 1) {
                            $this->tag .= $linkTag . $imageTag . '</a>';
                        } else {
                            $this->tag .= $imageTag;
                        }
                        $this->buildLink = true;
                        break;

                    case 'linkTag':
                        $this->tag .= $this->insertLink($data, $content, $url);
                        break;

                    case 'openingATag':
                        $this->tag .= $this->insertOpeningATag($data, $linkTag, $url);
                        break;

                    case 'linkText':
                        $this->tag .= $this->insertLinkText($data, $content);
                        break;

                    case 'closingATag':
                        $this->tag .= $this->insertClosingATag($data);
                        break;

                    case 'string':
                        $this->tag .= $this->insertString($data, $linkTag);
                        break;
                }
            }
        }
    }


    private function resolveImageName($image)
    {
        if (!strcmp(substr($image, 0, 4), 'EXT:')) {
            $image = PathUtility::getAbsoluteWebPath(GeneralUtility::getFileAbsFileName($image));
        }
        return $image;
    }



    /**
     * Adds an image.
     *
     * @param	array		$data
     * @param	string		$linkTag
     * @return	string
     */
    protected function insertImage(array $data, $linkTag)
    {
        $img = '';

        $image = $this->resolveImageName($data['image']);

        if (!empty($this->tag)) {
            if (isset($data['separator'])) {
                $img .= $data['separator'];
            } else {
                $img .= $this->separator;
            }
        }

        if (isset($data['image.']['link']) && $data['image.']['link'] == 1) {
            if (isset($data['image.']['alt'])) {
                $img .= $linkTag . '<img class="ico-mailto" width="18" height="16px" src="' . $image . '" alt="' . $data['image.']['alt'] . '" /></a>';
            } else {
                $img .= $linkTag . '<img class="ico-mailto" width="18" height="16px" src="' . $image . '" alt="" /></a>';
            }
        } else {
            if (isset($data['image.']['alt'])) {
                $img .= '<img class="ico-mailto" width="18" height="16px" src="' . $image . '" alt="' . $data['image.']['alt'] . '" />';
            } else {
                $img .= '<img class="ico-mailto" width="18" height="16px" src="' . $image . '" alt="" />';
            }
        }
        $this->buildLink = true;

        return $img;
    }

    /**
     * Adds a linktag.
     *
     * @param	array		$data
     * @param	string		$content
     * @param	string		$url
     * @return	string
     */
    protected function insertLink(array $data, $content, $url)
    {
        $link = '';

        if ($data['linkTag'] == 1) {
            if (!empty($this->tag)) {
                if (isset($data['separator'])) {
                    $link .= $data['separator'];
                } else {
                    $link .= $this->separator;
                }
            }

            if (isset($data['linkTag.']['title']) && !empty($data['linkTag.']['title'])) {
                $title = $data['linkTag.']['title'];

                if (preg_match('/##linkTag##/i', $title)) {
                    $title = str_replace('##linkTag##', $url, $title);
                }

                if (!preg_match('/title/i', $content)) {
                    $expr = '|^.*(<a.*)(>.*</a>.*)$|';
                    preg_match($expr, $content, $parts);
                    $link .= $parts[1] . ' title="' . $title . '" ' . $parts[2];
                } else {
                    $link .= $content;
                }
            } else {
                $link .= $content;
            }

            $this->buildLink = true;
        }

        return $link;
    }

    /**
     * Adds an opening A-tag.
     *
     * @param	array		$data
     * @param	string		$linkTag
     * @param	string url
     * @return	string
     */
    protected function insertOpeningATag(array $data, $linkTag, $url)
    {
        $openingATag = '';

        if (isset($data['openingATag']) && $data['openingATag'] == 1) {
            $openingATag .= $linkTag;

            // Insert given params
            if (isset($data['openingATag.']['params']) && !empty($data['openingATag.']['params'])) {
                $params = $data['openingATag.']['params'];

                // Insert url if necessary
                if (preg_match('/##linkTag##/i', $params)) {
                    $params = str_replace('##linkTag##', $url, $params);
                }

                $expr = '/^.*(<a.*)(>.*)$/';
                preg_match($expr, $openingATag, $parts);
                $openingATag = $parts[1] . ' ' . $params . ' ' . $parts[2];
            }

            // Insert given target
            if (isset($data['openingATag.']['target']) && !empty($data['openingATag.']['target'])) {
                $target = $data['openingATag.']['target'];

                if ($openingATag != ($str = preg_replace('/target="[^"]*"/', 'target="' . $target . '"', $openingATag))) {
                    $openingATag = $str;
                } else {
                    $expr = '/^.*(<a.*)(>.*)$/';
                    preg_match($expr, $openingATag, $parts);
                    $openingATag = $parts[1] . ' target="' . $target . '" ' . $parts[2];
                }
            }

            // Insert given title except a title is already set
            if (isset($data['openingATag.']['title']) && !empty($data['openingATag.']['title'])) {
                $title = $data['openingATag.']['title'];

                // Insert url if necessary
                if (preg_match('/##linkTag##/i', $title)) {
                    $title = str_replace('##linkTag##', $url, $title);
                }

                if (!preg_match('/title/i', $openingATag)) {
                    $expr = '/^.*(<a.*)(>.*)$/';
                    preg_match($expr, $openingATag, $parts);
                    $openingATag = $parts[1] . ' title="' . $title . '"' . $parts[2];
                }
            }
        }

        return $openingATag;
    }

    /**
     * Adds a link text.
     *
     * @param	array		$data
     * @param	string		$content
     * @return	string
     */
    protected function insertLinkText(array $data, $content)
    {
        $linkText = '';

        if (isset($data['linkText']) && $data['linkText'] == 1) {
            if (!empty($this->tag)) {
                if (isset($data['separator'])) {
                    // $linkText .= $data['separator'];
                } else {
                    // $linkText .= $this->separator;
                }
            }

            $expr = '/<a.+?>(.*)(<\/a>)/';
            $content = str_replace("\n", '', $content);
            preg_match($expr, $content, $parts);
            $linkText .= $parts[1];
        }

        return $linkText;
    }

    /**
     * Adds a closing A-tag.
     *
     * @param	array		$data
     * @return	string
     */
    protected function insertClosingATag(array $data)
    {
        $closingATag = '';

        if (isset($data['closingATag']) && $data['closingATag'] == 1) {
            $closingATag .= '</a>';
        }

        $this->buildLink = true;

        return $closingATag;
    }

    /**
     * Adds the filesize.
     *
     * @param	array		$data
     * @param	string		$url
     * @param	string		$linkTag
     * @return	string
     */
    protected function insertFilesize(array $data, $url, $linkTag)
    {
        $stringFilesize = '';

        if (($data['filesize'] == 1) && file_exists($url)) {
            if (!empty($this->tag)) {
                if (isset($data['separator'])) {
                    $stringFilesize .= $data['separator'];
                } else {
                    $stringFilesize .= $this->separator;
                }
            }

            $units = array(
                '0' => \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate('bytes', 'TmMlLinks'),
                '1' => \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate('KB', 'TmMlLinks'),
                '2' => \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate('MB', 'TmMlLinks'),
                '3' => \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate('GB', 'TmMlLinks'),
                '4' => \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate('TB', 'TmMlLinks'),
            );

            $j = 0;
            $filesize = filesize($url);
            for ($j = 0; $filesize >= 1024; $j++) {
                $filesize /= 1024;
            }

            $decimalPlaces = ($j < 2) ? 0 : $j - 1;

            $size = '(%.' . $decimalPlaces . 'f&nbsp;%s)';
            if (isset($data['filesize.']['link']) && $data['filesize.']['link'] == 1) {
                $stringFilesize .= $linkTag . sprintf($size, $filesize, $units[$j]) . '</a>';
            } else {
                $stringFilesize .= sprintf($size, $filesize, $units[$j]);
            }
            $this->buildLink = true;
        }

        return $stringFilesize;
    }

    /**
     * Adds the dimensions.
     *
     * @param	array		$data
     * @param	string		$url
     * @return	string
     */
    protected function insertDimensions(array $data, $url)
    {
        $dimensions = '';

        if (($data['dimensions'] == 1) && file_exists($url)) {
            if (!empty($this->tag)) {
                if (isset($data['separator'])) {
                    $dimensions .= $data['separator'];
                } else {
                    $dimensions .= $this->separator;
                }
            }

            $imgData = getimagesize($url);
            if (!empty($imgData)) {
                $dimensions .= sprintf('%sx%s', $imgData[0], $imgData[1]);
            }

            $this->buildLink = true;
        }

        return $dimensions;
    }

    /**
     * Adds the filename.
     *
     * @param	array		$data
     * @param	string		$url
     * @return	string
     */
    protected function insertFilename(array $data, $url)
    {
        $filename = '';

        if ($data['filename'] == 1) {
            if (!empty($this->tag)) {
                if (isset($data['separator'])) {
                    $filename .= $data['separator'];
                } else {
                    $filename .= $this->separator;
                }
            }

            $filename .= basename($url);
            $this->buildLink = true;
        }

        return $filename;
    }

    /**
     * Adds the revision date.
     *
     * @param	array		$data
     * @param	string		$url
     * @return	string
     */
    protected function insertRevisionDate(array $data, $url)
    {
        $date = '';

        if ($data['revisionDate'] == 1 && file_exists($url)) {
            if (!empty($this->tag)) {
                if (isset($data['separator'])) {
                    $date .= $data['separator'];
                } else {
                    $date .= $this->separator;
                }
            }

            $format = '%Y-%m-%d';
            if (isset($data['revisionDate.']['format'])) {
                $format = $data['revisionDate.']['format'];
            }

            $date .= strftime($format, filemtime($url));
            $this->buildLink = true;
        }

        return $date;
    }

    /**
     * Adds a string.
     *
     * @param	array		$data
     * @param	string		$linkTag
     * @return	string
     */
    protected function insertString(array $data, $linkTag)
    {
        $string = '';

        if (!empty($this->tag) && isset($data['separator'])) {
            $string .= $data['separator'];
        }

        if (isset($data['string.']['link']) && $data['string.']['link'] == 1) {
            $string .= $linkTag . $data['string'] . '</a>';
        } else {
            $string .= $data['string'];
        }
        $this->buildLink = true;

        return $string;
    }

    /**
     * Gets data of created link.
     *
     * @param	LinkResult $content
     * @param	string $conf
     * @return	string
     */
    public function getFiletype(LinkResult $content, $conf)
    {

        // Get file extension
        $file = basename($content->getUrl());
        $ext = preg_replace('/\?.*/', '', strtolower(pathinfo($file, PATHINFO_EXTENSION)));
        $ext = ($ext === 'jpeg') ? 'jpg' : $ext;

        $GLOBALS['TSFE']->register['fileType'] = $ext;

        // Get link type
        $GLOBALS['TSFE']->register['linkType'] = $content->getType();

        // Get link url
        $GLOBALS['TSFE']->register['url'] = $content->getUrl();

        // Get link tag
        $GLOBALS['TSFE']->register['tag'] = $content->getHtml();

        return $content->getHtml();
    }
}
