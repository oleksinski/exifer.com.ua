<?php
   /***************************************************************/
   /* SimpleRss - a class fetching and parsing RSS feeds

      Software License Agreement (BSD License)

      Copyright (C) 2005-2007, Edward Eliot.
      All rights reserved.

      Redistribution and use in source and binary forms, with or without
      modification, are permitted provided that the following conditions are met:

         * Redistributions of source code must retain the above copyright
           notice, this list of conditions and the following disclaimer.
         * Redistributions in binary form must reproduce the above copyright
           notice, this list of conditions and the following disclaimer in the
           documentation and/or other materials provided with the distribution.
         * Neither the name of Edward Eliot nor the names of its contributors
           may be used to endorse or promote products derived from this software
           without specific prior written permission of Edward Eliot.

      THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDER AND CONTRIBUTORS "AS IS" AND ANY
      EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
      WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
      DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY
      DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
      (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
      LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
      ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
      (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
      SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.

      Last Updated:  7th January 2007                             */
   /***************************************************************/

   // required files
   //require('http.inc.php');
   //require('php-cache.inc.php');

   // RssObject, RssChannel and RssItem are support objects which represent the structure of the returned data
   class RssObject {
      var $oChannel;
      var $aItems = array();

      function RssObject() {
         $this->oChannel = new RssChannel();
      }
   }

   class RssChannel {
      var $sTitle = '';
      var $sDescription = '';
      var $sLink = '';
      var $sDate = '';
      var $sGenerator = '';
      var $sLanguage = '';
      var $sCopyright = '';
      var $sManagingeditor = '';
   }

   class RssItem {
      var $sGuid = '';
      var $sTitle = '';
      var $sDescription = '';
      var $sLink = '';
      var $sDate = '';
      var $sAuthor = '';
      var $sCategory = '';
   }

   // main class
   class SimpleRss {
      var $oRssObject;
      var $iNumItems;
      var $sInputEncoding;
      var $sOutputEncoding;
      var $bInItem = false;
      var $bInChannel = false;
      var $sTag = '';
      var $iPointer = -1;
      var $sTempContent = '';
      var $bSuccessful = false;
      var $bCached = false;
      var $bStaleCache = false;

      // $iNumItems = -1 means whatever's in the feed
      // sInputEncoding - in PHP 4 default is ISO-8859-1, in PHP 5 detected automatically
      // sOutputEncoding - defaults to UTF-8
      function SimpleRss($sUrl, $iNumItems = -1, $sInputEncoding = '', $sOutputEncoding = 'UTF-8') {
         $this->oRssObject = new RssObject(); // this object holds the returned data
         $this->iNumItems = $iNumItems;
         $this->sInputEncoding = $sInputEncoding;
         $this->sOutputEncoding = $sOutputEncoding;

         $oHttp = new SimpleHttp();

         // request feed
         if ($sData = $oHttp->Get($sUrl)) {
            $this->bSuccessful = $this->Parse($sData);
         }

         // trim rss data
         $this->oRssObject = $this->GetRssObject();
      }

      function Parse($sData) {
         // set up XML parser
         if ($this->sInputEncoding != '' && version_compare(phpversion(), '5', '<')) {
            $oParser = xml_parser_create($this->sInputEncoding);
         } else {
            $oParser = xml_parser_create();
         }
         xml_parser_set_option($oParser, XML_OPTION_TARGET_ENCODING, $this->sOutputEncoding);
         // set scope of handler functions to this class
         xml_set_object($oParser, $this);
         // set handler functions
         xml_set_element_handler($oParser, 'StartTag', 'CloseTag');
         xml_set_character_data_handler($oParser, 'TagContent');
         // parse the data, set flag to indicate success or failure
         $bResult = xml_parse($oParser, $sData);
         // free memory used
         xml_parser_free($oParser);

         return $bResult;
      }

      // this function triggers each time the parser encounters a new tag
      function StartTag($oParser, $sName, $aAttributes) {
         if ($this->bInItem || ($this->bInChannel && $sName != 'ITEM')) {
            $this->sTag = $sName;
         } else {
            switch ($sName) {
               case 'ITEM':
                  $this->bInItem = true;
                  $this->iPointer++;

                  if ($this->iNumItems == -1 || $this->iPointer < $this->iNumItems) {
                     $this->oRssObject->aItems[] = new RssItem();
                  }
                  break;
               case 'CHANNEL':
                  $this->bInChannel = true;
                  break;
            }
         }
      }

      // this function triggers when the parser encounters a corresponding close tag
      function CloseTag($oParser, $sName) {
         if ($sName == 'ITEM') {
            if ($this->iNumItems == -1 || $this->iPointer < $this->iNumItems) {
               // if the feed contained a content element we'll override the description text with it as it's
               // likely to represent the entire article
               if ($this->sTempContent != '') {
                  $this->oRssObject->aItems[$this->iPointer]->sDescription = $this->sTempContent;
                  $this->sTempContent = '';
               }
            }
            $this->bInItem = false;
         } elseif ($sName == 'CHANNEL') {
            $this->bInChannel = false;
         }
      }

      // this function triggers when the parser encounters content for the current tag
      function TagContent($oParser, $sData) {
         // is the parser looking at an item
         if ($this->bInItem) {
            if ($this->iNumItems == -1 || $this->iPointer < $this->iNumItems) {
               switch ($this->sTag) {
                  case 'GUID':
                     $this->oRssObject->aItems[$this->iPointer]->sGuid .= $sData;
                     break;
                  case 'TITLE':
                     $this->oRssObject->aItems[$this->iPointer]->sTitle .= $sData;
                     break;
                  case 'DESCRIPTION':
                     $this->oRssObject->aItems[$this->iPointer]->sDescription .= $sData;
                     break;
                  case 'CONTENT:ENCODED':
                     $this->sTempContent .= $sData;
                     break;
                  case 'LINK':
                     $this->oRssObject->aItems[$this->iPointer]->sLink .= $sData;
                     break;
                  case 'PUBDATE':
                  case 'DC:DATE':
                     $this->oRssObject->aItems[$this->iPointer]->sDate .= $sData;
                     break;
                  case 'AUTHOR':
                  case 'DC:CREATOR':
                     $this->oRssObject->aItems[$this->iPointer]->sAuthor .= $sData;
                     break;
                  case 'CATEGORY':
                     $this->oRssObject->aItems[$this->iPointer]->sCategory .= $sData;
                     break;
               }
            }
         } elseif ($this->bInChannel) { // is the parser looking at global channel data
            switch ($this->sTag) {
               case 'TITLE':
                  $this->oRssObject->oChannel->sTitle .= $sData;
                  break;
               case 'DESCRIPTION':
                  $this->oRssObject->oChannel->sDescription .= $sData;
                  break;
               case 'LINK':
                  $this->oRssObject->oChannel->sLink .= $sData;
                  break;
               case 'COPYRIGHT':
                  $this->oRssObject->oChannel->sCopyright .= $sData;
                  break;
               case 'MANAGINGEDITOR':
                  $this->oRssObject->oChannel->sManagingeditor .= $sData;
                  break;
               case 'PUBDATE':
                  $this->oRssObject->oChannel->sDate .= $sData;
                  break;
               case 'GENERATOR':
                  $this->oRssObject->oChannel->sGenerator .= $sData;
                  break;
               case 'LANGUAGE':
                  $this->oRssObject->oChannel->sLanguage .= $sData;
                  break;
            }
         }
      }

      // gets the returned feed data structure
      function GetRssObject() {
         if ($this->bSuccessful) {
            $cannelVars = get_object_vars($this->oRssObject->oChannel);
            foreach($cannelVars as $var=>$value) {
               if(is_string($var)) {
                  $this->oRssObject->oChannel->$var = _trim($value);
               }
            }
            foreach($this->oRssObject->aItems as $index=>$item) {
               $itemVars = get_object_vars($item);
               foreach($itemVars as $var=>$value) {
                  if(is_string($var)) {
                     $this->oRssObject->aItems[$index]->$var = _trim($value);
                  }
               }
            }
            return $this->oRssObject;
         } else {
            return false;
         }
      }

      // indicates whether the feed data was retrieved from cache
      function IsCached() {
         return $this->bCached;
      }

      // indicates whether the cached data returned was stale - therefore an http request to get live data failed
      function IsStaleCache() {
         return $this->bStaleCache;
      }
   }
?>