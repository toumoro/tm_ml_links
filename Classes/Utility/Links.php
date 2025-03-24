<?php

namespace Toumoro\TmMlLinks\Utility;

class Links  {

    public $buildLink = false;
    /**
     * Main action
     *
     */
    public function main($content, $conf) {

        $this->settings = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_tmmllinks.'];

	$fileType ='';
	if (isset($GLOBALS['TSFE']->register['fileType'])) {
	    $fileType = $GLOBALS['TSFE']->register['fileType'];
	}
        $linkType = $GLOBALS['TSFE']->register['linkType'];
        $linkTag = $GLOBALS['TSFE']->register['tag'];
        $url = urldecode($GLOBALS['TSFE']->register['url']);
        // Use given seperator
        $this->separator = isset($this->settings['separator']) ? $this->settings['separator'] : ' ';
        $this->tag = '';

        // Go through configuration and modify the link
        switch ($linkType) {
            case 'file':
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
        
        if (!$this->buildLink ?? false) {
            $this->tag = $content;
        }

        return str_replace("&amp;","&",$this->tag);
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
    protected function prepareFileLink($content, $fileType, $linkType, $linkTag, $url) {
        // Check if there is anything defined for this filetype and if the file exists
        
        $fileType = explode('?',$fileType);
        $fileType = $fileType[0];

        if (isset($this->settings[$fileType . '.'])) {

            $settings = $this->settings[$fileType . '.'];
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
    protected function prepareMailtoLink($content, $fileType, $linkType, $linkTag, $url) {
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
    protected function preparePageLink($content, $fileType, $linkType, $linkTag, $url) {
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
    protected function prepareUrlLink($content, $fileType, $linkType, $linkTag, $url) {
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

                        if (strpos($content,'<img') !== FALSE) {
                            break;
                        }
                        if (!empty($this->tag)) {
                            $this->tag .= $this->separator;
                        }

                        // Get filetype
                        $file = basename($url);
                        if (preg_match('/(.*)\.([^\.]*$)/', $file, $reg)) {
                            $ext = strtolower($reg[2]);
                            $ext = ($ext === 'jpeg') ? 'jpg' : $ext;
                        }

                        // Add image
                        if (isset($data['image.'][$ext])) {
                            $image = $data['image.'][$ext];
                            $alt = isset($data['image.'][$ext]['alt']) ? $data['image.'][$ext]['alt'] : '';
                        } else {
                            $image = $data['image'];
                            $alt = isset($data['image.']['alt']) ? $data['image.']['alt'] : '';
                        }
                        if (!strcmp(substr($image, 0, 4), 'EXT:')) {
                            // Get rid of 'EXT:'
                            $image = substr($image, 4);
                            list($ext, $path) = explode('/', $image, 2);
                            $extRelPath = substr(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($ext), strlen(\TYPO3\CMS\Core\Core\Environment::getPublicPath() . '/'));
                            $image = $extRelPath . $path;
                        }
                        $imageTag = file_exists($image) ? '<img src="' . $image . '" alt="' . $alt . '"/>' : '';

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

    /**
     * Adds an image.
     *
     * @param	array		$data
     * @param	string		$linkTag
     * @return	string
     */
    protected function insertImage(array $data, $linkTag) {
        $img = '';

        $image = $data['image'];
        if (!strcmp(substr($image, 0, 4), 'EXT:')) {
            // Get rid of 'EXT:'
            $image = substr($image, 4);
            list($ext, $path) = explode('/', $image, 2);
            $extRelPath = substr(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($ext), strlen(\TYPO3\CMS\Core\Core\Environment::getPublicPath() . '/'));
            $image = $extRelPath . $path;
        }

        if (file_exists($image)) {
            if (!empty($this->tag)) {
                if (isset($data['separator'])) {
                    $img .= $data['separator'];
                } else {
                    $img .= $this->separator;
                }
            }

            if (isset($data['image.']['link']) && $data['image.']['link'] == 1) {
                if (isset($data['image.']['alt'])) {
                    $img .= $linkTag . '<img src="' . $image . '" alt="' . $data['image.']['alt'] . '" /></a>';
                } else {
                    $img .= $linkTag . '<img src="' . $image . '" alt="" /></a>';
                }
            } else {
                if (isset($data['image.']['alt'])) {
                    $img .= '<img src="' . $image . '" alt="' . $data['image.']['alt'] . '" />';
                } else {
                    $img .= '<img src="' . $image . '" alt="" />';
                }
            }
            $this->buildLink = true;
        }

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
    protected function insertLink(array $data, $content, $url) {
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
    protected function insertOpeningATag(array $data, $linkTag, $url) {
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
    protected function insertLinkText(array $data, $content) {
        $linkText = '';

        if (isset($data['linkText']) && $data['linkText'] == 1) {
            if (!empty($this->tag)) {
                if (isset($data['separator'])) {
                    $linkText .= $data['separator'];
                } else {
                    $linkText .= $this->separator;
                }
            }

            $expr = '/<a.+?>(.*)(<\/a>)/';
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
    protected function insertClosingATag(array $data) {
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
    protected function insertFilesize(array $data, $url, $linkTag) {
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
                '0' => $this->pi_getLL('bytes'),
                '1' => $this->pi_getLL('KB'),
                '2' => $this->pi_getLL('MB'),
                '3' => $this->pi_getLL('GB'),
                '4' => $this->pi_getLL('TB'),
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
    protected function insertDimensions(array $data, $url) {
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
    protected function insertFilename(array $data, $url) {
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
    protected function insertRevisionDate(array $data, $url) {
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
    protected function insertString(array $data, $linkTag) {
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
     * @param	string		$content
     * @param	array		$conf
     * @return	string
     */
    public function getFiletype(array $content, $conf) {
        // Get file extension
        $file = basename($content['url']);
        if (preg_match('/(.*)\.([^\.]*$)/', $file, $reg)) {
            $ext = strtolower($reg[2]);
            $ext = ($ext === 'jpeg') ? 'jpg' : $ext;
	    $GLOBALS['TSFE']->register['fileType'] = $ext;
        }

        // Get link type
        $GLOBALS['TSFE']->register['linkType'] = $content['TYPE'];

        // Get link url
        $GLOBALS['TSFE']->register['url'] = $content['url'];

        // Get link tag
        $GLOBALS['TSFE']->register['tag'] = $content['TAG'];

        return $content['TAG'];
    }

}
