<?php
namespace SynergyCommon;

use Laminas\Stdlib\AbstractOptions;

/**
 * Class CommonSiteSettings
 *
 * @package SynergyCommon
 */
class CommonSiteSettings extends AbstractOptions
{
    //amazon
    protected $amazonTag;
    protected $amazonApiKey;
    protected $amazonSecret;

    protected $tradeDoublerToken;

    //facebook
    protected $facebookApiId;
    protected $facebookUserId;
    protected $facebookFanPage;

    //google
    protected $googlePage;
    protected $googleAnalyticsId;
    protected $googleAuthorId;
    protected $googleVerification;

    //site
    protected $homePageOfferCount;
    protected $locale;
    protected $language;
    protected $region;
    protected $script;

    //solr
    protected $offerCore = 'offer';
    protected $merchantCore = 'merchant';
    protected $categoryCore = 'category';
    protected $solrIP = 'localhost';
    protected $solrPort = 8449;

    //verification code
    protected $bingVerification;

    //Affiliate Window
    protected $affiliateWindowVerification;

    //Webgains
    protected $webgainsCampaignId;
    protected $webgainsNetwork;

    protected $skimlinksId;
    protected $clickref;
    protected $joinedOnly = 0;
    /** @var  string */
    protected $affPublisherId;
    protected $groupOnToken;
    protected $groupOnCode;
    protected $groupOnAffId;
    protected $yieldKitSiteId;
    protected $cjWebsiteId; //commission Junction site id
    protected $daisyConMediaId = '';
    protected $amazonDomainTag;

    public function __construct($options = null, $mode = true)
    {
        $this->setStrictMode($mode);
        $data = array();
        if ($options) {
            foreach ($options as $key => $value) {
                $newKey        = str_replace('-', '_', $key);
                $data[$newKey] = $value;
            }
        }
        parent::__construct($data);
    }

    /**
     * @return mixed
     */
    public function getAmazonDomainTag()
    {
        return $this->amazonDomainTag;
    }

    /**
     * @param mixed $amazonDomainTag
     */
    public function setAmazonDomainTag($amazonDomainTag)
    {
        $this->amazonDomainTag = $amazonDomainTag;
    }

    /**
     * @return mixed
     */
    public function getYieldKitSiteId()
    {
        return $this->yieldKitSiteId;
    }

    /**
     * @param mixed $yieldKitSiteId
     */
    public function setYieldKitSiteId($yieldKitSiteId)
    {
        $this->yieldKitSiteId = $yieldKitSiteId;
    }

    /**
     * @return string
     */
    public function getDaisyConMediaId()
    {
        return $this->daisyConMediaId;
    }

    /**
     * @param string $daisyConMediaId
     */
    public function setDaisyConMediaId($daisyConMediaId)
    {
        $this->daisyConMediaId = $daisyConMediaId;
    }

    /**
     * @return mixed
     */
    public function getCjWebsiteId()
    {
        return $this->cjWebsiteId;
    }

    /**
     * @param mixed $cjWebsiteId
     */
    public function setCjWebsiteId($cjWebsiteId)
    {
        $this->cjWebsiteId = $cjWebsiteId;
    }

    /**
     * @return mixed
     */
    public function getGroupOnCode()
    {
        return $this->groupOnCode;
    }

    /**
     * @param mixed $groupOnCode
     */
    public function setGroupOnCode($groupOnCode)
    {
        $this->groupOnCode = $groupOnCode;
    }

    /**
     * @return mixed
     */
    public function getGroupOnAffId()
    {
        return $this->groupOnAffId;
    }

    /**
     * @param mixed $groupOnAffId
     */
    public function setGroupOnAffId($groupOnAffId)
    {
        $this->groupOnAffId = $groupOnAffId;
    }

    /**
     * @return mixed
     */
    public function getGroupOnToken()
    {
        return $this->groupOnToken;
    }

    /**
     * @param mixed $groupOnToken
     */
    public function setGroupOnToken($groupOnToken)
    {
        $this->groupOnToken = $groupOnToken;
    }

    /**
     * @return int
     */
    public function getJoinedOnly()
    {
        return $this->joinedOnly;
    }

    /**
     * @return string
     */
    public function getAffPublisherId()
    {
        return $this->affPublisherId;
    }

    /**
     * @param string $affPublisherId
     */
    public function setAffPublisherId($affPublisherId)
    {
        $this->affPublisherId = $affPublisherId;
    }

    /**
     * @param int $joinedOnly
     */
    public function setJoinedOnly($joinedOnly)
    {
        $this->joinedOnly = $joinedOnly;
    }

    public function setScript($script)
    {
        $this->script = $script;
    }

    public function getScript()
    {
        return $this->script;
    }

    public function setAffiliateWindowVerification($affiliateWindowVerification)
    {
        $this->affiliateWindowVerification = $affiliateWindowVerification;
    }

    public function getAffiliateWindowVerification()
    {
        return $this->affiliateWindowVerification;
    }

    public function setBingVerification($bingVerification)
    {
        $this->bingVerification = $bingVerification;
    }

    public function getBingVerification()
    {
        return $this->bingVerification;
    }

    public function setClickref($clickref)
    {
        $this->clickref = $clickref;
    }

    public function getClickref()
    {
        return $this->clickref;
    }

    public function setGoogleVerification($googleVerification)
    {
        $this->googleVerification = $googleVerification;
    }

    public function getGoogleVerification()
    {
        return $this->googleVerification;
    }

    public function setSkimlinksId($skimlinksId)
    {
        $this->skimlinksId = $skimlinksId;
    }

    public function getSkimlinksId()
    {
        return $this->skimlinksId;
    }

    public function setWebgainsCampaignId($webgainsCampaignId)
    {
        $this->webgainsCampaignId = $webgainsCampaignId;
    }

    public function getWebgainsCampaignId()
    {
        return $this->webgainsCampaignId;
    }

    public function setWebgainsNetwork($webgainsNetwork)
    {
        $this->webgainsNetwork = $webgainsNetwork;
    }

    public function getWebgainsNetwork()
    {
        return $this->webgainsNetwork;
    }

    public function setSolrPort($solrPort)
    {
        $this->solrPort = $solrPort;
    }

    public function getSolrPort()
    {
        return $this->solrPort;
    }

    public function setSolrIP($solrIP)
    {
        $this->solrIP = $solrIP;
    }

    public function getSolrIP()
    {
        return $this->solrIP;
    }

    public function setCategoryCore($categoryCore)
    {
        $this->categoryCore = $categoryCore;
    }

    public function getCategoryCore()
    {
        return $this->categoryCore;
    }

    public function setMerchantCore($merchantCore)
    {
        $this->merchantCore = $merchantCore;
    }

    public function getMerchantCore()
    {
        return $this->merchantCore;
    }

    public function setOfferCore($offerCore)
    {
        $this->offerCore = $offerCore;
    }

    public function getOfferCore()
    {
        return $this->offerCore;
    }

    public function setLanguage($language)
    {
        $this->language = $language;
    }

    public function getLanguage()
    {
        return $this->language;
    }

    public function setLocale($locale)
    {
        $this->locale = $locale;
    }

    public function getLocale()
    {
        return $this->locale;
    }

    public function setRegion($region)
    {
        $this->region = $region;
    }

    public function getRegion()
    {
        return $this->region;
    }

    public function setAmazonApiKey($amazonApiKey)
    {
        $this->amazonApiKey = $amazonApiKey;
    }

    public function getAmazonApiKey()
    {
        return $this->amazonApiKey;
    }

    public function setAmazonSecret($amazonSecret)
    {
        $this->amazonSecret = $amazonSecret;
    }

    public function getAmazonSecret()
    {
        return $this->amazonSecret;
    }

    public function setAmazonTag($amazonTag)
    {
        $this->amazonTag = $amazonTag;
    }

    public function getAmazonTag()
    {
        return $this->amazonTag;
    }

    public function setFacebookApiId($facebookApiId)
    {
        $this->facebookApiId = $facebookApiId;
    }

    public function getFacebookApiId()
    {
        return $this->facebookApiId;
    }

    public function setFacebookFanPage($facebookFanPage)
    {
        $this->facebookFanPage = $facebookFanPage;
    }

    public function getFacebookFanPage()
    {
        return $this->facebookFanPage;
    }

    public function setFacebookUserId($facebookUserId)
    {
        $this->facebookUserId = $facebookUserId;
    }

    public function getFacebookUserId()
    {
        return $this->facebookUserId;
    }

    public function setGoogleAnalyticsId($googleAnalyticsId)
    {
        $this->googleAnalyticsId = $googleAnalyticsId;
    }

    public function getGoogleAnalyticsId()
    {
        return $this->googleAnalyticsId;
    }

    public function setGoogleAuthorId($googleAuthorId)
    {
        $this->googleAuthorId = $googleAuthorId;
    }

    public function getGoogleAuthorId()
    {
        return $this->googleAuthorId;
    }

    public function setGooglePage($googlePage)
    {
        $this->googlePage = $googlePage;
    }

    public function getGooglePage()
    {
        return $this->googlePage;
    }

    public function setHomePageOfferCount($homePageOfferCount)
    {
        $this->homePageOfferCount = $homePageOfferCount;
    }

    public function getHomePageOfferCount()
    {
        return $this->homePageOfferCount;
    }

    public function setTradeDoublerToken($tradeDoublerToken)
    {
        $this->tradeDoublerToken = $tradeDoublerToken;
    }

    public function getTradeDoublerToken()
    {
        return $this->tradeDoublerToken;
    }

    /**
     * @param boolean $_strictMode__
     */
    public function setStrictMode($_strictMode__)
    {
        $this->__strictMode__ = $_strictMode__;
    }
}
