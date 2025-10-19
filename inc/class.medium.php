<?php

class wp_automatic_MediumScraper
{
    public $ch;
    public $cursor = '';


    public function __construct(&$ch)
    {
        $this->ch = $ch;
    }

    //fetchArticles by url check the url and decide if it is by tag,search or user
    public function fetchArticles(string $url, string $start = '', string $type = 'tag')
    {
        //check if the url is a tag, search or user
        if ($type == 'tag') {
            //it's a tag
            $parts = explode('tag/', $url);
            $tag = trim($parts[1], '/');

            //explode the tag by /
            $tag_parts = explode('/', $tag);

            //first part is the tag name
            $tag = $tag_parts[0];

            return $this->fetchByTag($tag, $start);
        } elseif ($type == 'search') {
            //it's a search
            
            return $this->fetchBySearch($url, $start);
        } elseif ($type == 'user') {

            $user = $url;

            return $this->fetchByUser($user, $start);
        } else {
            //default fetch by URL
            return $this->fetchByUrl($url, $start);
        }
    }
    /**
     * Fetch articles by tag.
     *
     * @param string $tag
     * @param int $page
     * @return string Raw HTML or JSON
     */
    public function fetchByTag(string $tag, string $page = '')
    {

        echo '<br>Fetching articles by tag: ' . $tag;
        echo '<br>Using cursor: ' . $page;

        //sample json
        $sample_json = '[{"operationName":"TagArchiveFeedQuery","variables":{"tagSlug":"liverpool","timeRange":{"kind":"ALL_TIME"},"sortOrder":"NEWEST","first":10,"after":""},"query":"query TagArchiveFeedQuery($tagSlug: String!, $timeRange: TagPostsTimeRange!, $sortOrder: TagPostsSortOrder!, $first: Int!, $after: String) {\\n  tagFromSlug(tagSlug: $tagSlug) {\\n    id\\n    sortedFeed: posts(\\n      timeRange: $timeRange\\n      sortOrder: $sortOrder\\n      first: $first\\n      after: $after\\n    ) {\\n      ...TagPosts_tagPostConnection\\n      __typename\\n    }\\n    mostReadFeed: posts(timeRange: $timeRange, sortOrder: MOST_READ, first: $first) {\\n      ...TagPosts_tagPostConnection\\n      __typename\\n    }\\n    __typename\\n  }\\n}\\n\\nfragment StreamPostPreviewImage_imageMetadata on ImageMetadata {\\n  id\\n  focusPercentX\\n  focusPercentY\\n  alt\\n  __typename\\n}\\n\\nfragment StreamPostPreviewImage_post on Post {\\n  title\\n  previewImage {\\n    ...StreamPostPreviewImage_imageMetadata\\n    __typename\\n    id\\n  }\\n  __typename\\n  id\\n}\\n\\nfragment SignInOptions_user on User {\\n  id\\n  name\\n  imageId\\n  __typename\\n}\\n\\nfragment SignUpOptions_user on User {\\n  id\\n  name\\n  imageId\\n  __typename\\n}\\n\\nfragment SusiModal_user on User {\\n  ...SignInOptions_user\\n  ...SignUpOptions_user\\n  __typename\\n  id\\n}\\n\\nfragment SusiClickable_user on User {\\n  ...SusiModal_user\\n  __typename\\n  id\\n}\\n\\nfragment SusiModal_post on Post {\\n  id\\n  creator {\\n    id\\n    __typename\\n  }\\n  __typename\\n}\\n\\nfragment SusiClickable_post on Post {\\n  id\\n  mediumUrl\\n  ...SusiModal_post\\n  __typename\\n}\\n\\nfragment MultiVoteCount_post on Post {\\n  id\\n  __typename\\n}\\n\\nfragment MultiVote_post on Post {\\n  id\\n  creator {\\n    id\\n    ...SusiClickable_user\\n    __typename\\n  }\\n  isPublished\\n  ...SusiClickable_post\\n  collection {\\n    id\\n    slug\\n    __typename\\n  }\\n  isLimitedState\\n  ...MultiVoteCount_post\\n  __typename\\n}\\n\\nfragment PostPreviewFooterSocial_post on Post {\\n  id\\n  ...MultiVote_post\\n  allowResponses\\n  isPublished\\n  isLimitedState\\n  postResponses {\\n    count\\n    __typename\\n  }\\n  __typename\\n}\\n\\nfragment AddToCatalogBase_post on Post {\\n  id\\n  isPublished\\n  ...SusiClickable_post\\n  __typename\\n}\\n\\nfragment AddToCatalogBookmarkButton_post on Post {\\n  ...AddToCatalogBase_post\\n  __typename\\n  id\\n}\\n\\nfragment BookmarkButton_post on Post {\\n  visibility\\n  ...SusiClickable_post\\n  ...AddToCatalogBookmarkButton_post\\n  __typename\\n  id\\n}\\n\\nfragment useNewsletterV3Subscription_newsletterV3 on NewsletterV3 {\\n  id\\n  type\\n  slug\\n  name\\n  collection {\\n    slug\\n    __typename\\n    id\\n  }\\n  user {\\n    id\\n    name\\n    username\\n    newsletterV3 {\\n      id\\n      __typename\\n    }\\n    __typename\\n  }\\n  __typename\\n}\\n\\nfragment useNewsletterV3Subscription_user on User {\\n  id\\n  username\\n  newsletterV3 {\\n    ...useNewsletterV3Subscription_newsletterV3\\n    __typename\\n    id\\n  }\\n  __typename\\n}\\n\\nfragment useAuthorFollowSubscribeButton_user on User {\\n  id\\n  name\\n  ...useNewsletterV3Subscription_user\\n  __typename\\n}\\n\\nfragment useAuthorFollowSubscribeButton_newsletterV3 on NewsletterV3 {\\n  id\\n  name\\n  ...useNewsletterV3Subscription_newsletterV3\\n  __typename\\n}\\n\\nfragment AuthorFollowSubscribeButton_user on User {\\n  id\\n  name\\n  imageId\\n  ...SusiModal_user\\n  ...useAuthorFollowSubscribeButton_user\\n  newsletterV3 {\\n    id\\n    ...useAuthorFollowSubscribeButton_newsletterV3\\n    __typename\\n  }\\n  __typename\\n}\\n\\nfragment FollowMenuOptions_user on User {\\n  id\\n  ...AuthorFollowSubscribeButton_user\\n  __typename\\n}\\n\\nfragment FollowMenuOptions_collection on Collection {\\n  id\\n  name\\n  __typename\\n}\\n\\nfragment ClapMutation_post on Post {\\n  __typename\\n  id\\n  clapCount\\n  ...MultiVoteCount_post\\n}\\n\\nfragment OverflowMenuItemUndoClaps_post on Post {\\n  id\\n  clapCount\\n  ...ClapMutation_post\\n  __typename\\n}\\n\\nfragment NegativeSignalModal_publisher on Publisher {\\n  __typename\\n  id\\n  name\\n}\\n\\nfragment NegativeSignalModal_post on Post {\\n  id\\n  creator {\\n    ...NegativeSignalModal_publisher\\n    viewerEdge {\\n      id\\n      isMuting\\n      __typename\\n    }\\n    __typename\\n    id\\n  }\\n  collection {\\n    ...NegativeSignalModal_publisher\\n    viewerEdge {\\n      id\\n      isMuting\\n      __typename\\n    }\\n    __typename\\n    id\\n  }\\n  __typename\\n}\\n\\nfragment ExplicitSignalMenuOptions_post on Post {\\n  ...NegativeSignalModal_post\\n  __typename\\n  id\\n}\\n\\nfragment OverflowMenu_post on Post {\\n  id\\n  creator {\\n    id\\n    ...FollowMenuOptions_user\\n    __typename\\n  }\\n  collection {\\n    id\\n    ...FollowMenuOptions_collection\\n    __typename\\n  }\\n  ...OverflowMenuItemUndoClaps_post\\n  ...AddToCatalogBase_post\\n  ...ExplicitSignalMenuOptions_post\\n  __typename\\n}\\n\\nfragment OverflowMenuButton_post on Post {\\n  id\\n  visibility\\n  ...OverflowMenu_post\\n  __typename\\n}\\n\\nfragment PostPreviewFooterMenu_post on Post {\\n  id\\n  ...BookmarkButton_post\\n  ...OverflowMenuButton_post\\n  __typename\\n}\\n\\nfragment usePostPublishedAt_post on Post {\\n  firstPublishedAt\\n  latestPublishedAt\\n  pinnedAt\\n  __typename\\n  id\\n}\\n\\nfragment Star_post on Post {\\n  id\\n  creator {\\n    id\\n    __typename\\n  }\\n  isLocked\\n  __typename\\n}\\n\\nfragment PostPreviewFooterMeta_post on Post {\\n  isLocked\\n  postResponses {\\n    count\\n    __typename\\n  }\\n  ...usePostPublishedAt_post\\n  ...Star_post\\n  __typename\\n  id\\n}\\n\\nfragment PostPreviewFooter_post on Post {\\n  ...PostPreviewFooterSocial_post\\n  ...PostPreviewFooterMenu_post\\n  ...PostPreviewFooterMeta_post\\n  __typename\\n  id\\n}\\n\\nfragment userUrl_user on User {\\n  __typename\\n  id\\n  customDomainState {\\n    live {\\n      domain\\n      __typename\\n    }\\n    __typename\\n  }\\n  hasSubdomain\\n  username\\n}\\n\\nfragment UserAvatar_user on User {\\n  __typename\\n  id\\n  imageId\\n  membership {\\n    tier\\n    __typename\\n    id\\n  }\\n  name\\n  username\\n  ...userUrl_user\\n}\\n\\nfragment PostPreviewBylineAuthorAvatar_user on User {\\n  ...UserAvatar_user\\n  __typename\\n  id\\n}\\n\\nfragment isUserVerifiedBookAuthor_user on User {\\n  verifications {\\n    isBookAuthor\\n    __typename\\n  }\\n  __typename\\n  id\\n}\\n\\nfragment UserLink_user on User {\\n  ...userUrl_user\\n  __typename\\n  id\\n}\\n\\nfragment UserName_user on User {\\n  id\\n  name\\n  ...isUserVerifiedBookAuthor_user\\n  ...UserLink_user\\n  __typename\\n}\\n\\nfragment PostPreviewByLineAuthor_user on User {\\n  ...PostPreviewBylineAuthorAvatar_user\\n  ...UserName_user\\n  __typename\\n  id\\n}\\n\\nfragment collectionUrl_collection on Collection {\\n  id\\n  domain\\n  slug\\n  __typename\\n}\\n\\nfragment CollectionAvatar_collection on Collection {\\n  name\\n  avatar {\\n    id\\n    __typename\\n  }\\n  ...collectionUrl_collection\\n  __typename\\n  id\\n}\\n\\nfragment SignInOptions_collection on Collection {\\n  id\\n  name\\n  __typename\\n}\\n\\nfragment SignUpOptions_collection on Collection {\\n  id\\n  name\\n  __typename\\n}\\n\\nfragment SusiModal_collection on Collection {\\n  name\\n  ...SignInOptions_collection\\n  ...SignUpOptions_collection\\n  __typename\\n  id\\n}\\n\\nfragment PublicationFollowSubscribeButton_collection on Collection {\\n  id\\n  slug\\n  name\\n  ...SusiModal_collection\\n  __typename\\n}\\n\\nfragment EntityPresentationRankedModulePublishingTracker_entity on RankedModulePublishingEntity {\\n  __typename\\n  ... on Collection {\\n    id\\n    __typename\\n  }\\n  ... on User {\\n    id\\n    __typename\\n  }\\n}\\n\\nfragment CollectionTooltip_collection on Collection {\\n  id\\n  name\\n  slug\\n  description\\n  subscriberCount\\n  customStyleSheet {\\n    header {\\n      backgroundImage {\\n        id\\n        __typename\\n      }\\n      __typename\\n    }\\n    __typename\\n    id\\n  }\\n  ...CollectionAvatar_collection\\n  ...PublicationFollowSubscribeButton_collection\\n  ...EntityPresentationRankedModulePublishingTracker_entity\\n  __typename\\n}\\n\\nfragment CollectionLinkWithPopover_collection on Collection {\\n  name\\n  ...collectionUrl_collection\\n  ...CollectionTooltip_collection\\n  __typename\\n  id\\n}\\n\\nfragment PostPreviewByLineCollection_collection on Collection {\\n  ...CollectionAvatar_collection\\n  ...CollectionTooltip_collection\\n  ...CollectionLinkWithPopover_collection\\n  __typename\\n  id\\n}\\n\\nfragment PostPreviewByLine_post on Post {\\n  creator {\\n    ...PostPreviewByLineAuthor_user\\n    __typename\\n    id\\n  }\\n  collection {\\n    ...PostPreviewByLineCollection_collection\\n    __typename\\n    id\\n  }\\n  __typename\\n  id\\n}\\n\\nfragment PostPreviewInformation_post on Post {\\n  readingTime\\n  isLocked\\n  ...Star_post\\n  ...usePostPublishedAt_post\\n  __typename\\n  id\\n}\\n\\nfragment StreamPostPreviewContent_post on Post {\\n  id\\n  title\\n  previewImage {\\n    id\\n    __typename\\n  }\\n  extendedPreviewContent {\\n    subtitle\\n    __typename\\n  }\\n  ...StreamPostPreviewImage_post\\n  ...PostPreviewFooter_post\\n  ...PostPreviewByLine_post\\n  ...PostPreviewInformation_post\\n  __typename\\n}\\n\\nfragment PostScrollTracker_post on Post {\\n  id\\n  collection {\\n    id\\n    __typename\\n  }\\n  sequence {\\n    sequenceId\\n    __typename\\n  }\\n  __typename\\n}\\n\\nfragment usePostUrl_post on Post {\\n  id\\n  creator {\\n    ...userUrl_user\\n    __typename\\n    id\\n  }\\n  collection {\\n    id\\n    domain\\n    slug\\n    __typename\\n  }\\n  isSeries\\n  mediumUrl\\n  sequence {\\n    slug\\n    __typename\\n  }\\n  uniqueSlug\\n  __typename\\n}\\n\\nfragment PostPreviewContainer_post on Post {\\n  id\\n  extendedPreviewContent {\\n    isFullContent\\n    __typename\\n  }\\n  visibility\\n  pinnedAt\\n  ...PostScrollTracker_post\\n  ...usePostUrl_post\\n  __typename\\n}\\n\\nfragment StreamPostPreview_post on Post {\\n  id\\n  ...StreamPostPreviewContent_post\\n  ...PostPreviewContainer_post\\n  __typename\\n}\\n\\nfragment TagPosts_tagPostConnection on TagPostConnection {\\n  edges {\\n    cursor\\n    node {\\n      id\\n      ...StreamPostPreview_post\\n      __typename\\n    }\\n    __typename\\n  }\\n  pageInfo {\\n    hasNextPage\\n    endCursor\\n    __typename\\n  }\\n  __typename\\n}\\n"}]';

        //decode json to be an array so we can modify desired parts
        $arr = json_decode($sample_json, true);

        //set the variables tagSlug
        $arr[0]['variables']['tagSlug'] = $tag;

        //set after
        if (!empty($page)) {
            $arr[0]['variables']['after'] = $page;
        }

        //convert the array back to json
        $json = json_encode($arr);

        curl_setopt($this->ch, CURLOPT_POST, true);

        curl_setopt_array($this->ch, array(
            CURLOPT_URL => 'https://medium.com/_/graphql',
            CURLOPT_POSTFIELDS => $json,
            CURLOPT_HTTPHEADER => array(
                'accept: */*',
                'accept-language: en-US,en;q=0.9,ar;q=0.8',
                'apollographql-client-name: lite',
                'apollographql-client-version: main-20250725-202833-18a1775cf9',
                'content-type: application/json',
                'graphql-operation: TopicCuratedListQuery',
                'medium-frontend-app: lite/main-20250725-202833-18a1775cf9',
                'medium-frontend-path: /tag/ai',
                'medium-frontend-route: tag',
                'origin: https://medium.com',
                'priority: u=1, i',
                'referer: https://medium.com/tag/ai',
                'sec-ch-ua: "Not)A;Brand";v="8", "Chromium";v="138", "Google Chrome";v="138"',
                'sec-ch-ua-mobile: ?0',
                'sec-ch-ua-platform: "macOS"',
                'sec-fetch-dest: empty',
                'sec-fetch-mode: cors',
                'sec-fetch-site: same-origin',
                'user-agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36',

            ),
        ));

        $response = curl_exec($this->ch);



        //error
        if (curl_errno($this->ch)) {
            throw new Exception('Curl error: ' . curl_error($this->ch));
        }

        //empty response
        if (empty($response)) {
            throw new Exception('Empty response from Medium API');
        }

        //json decode
        $response = json_decode($response, true);


        //if json decode failed
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('JSON decode error: ' . json_last_error_msg());
        }

        //check if desired path exists
        if (!isset($response[0]['data'])) {
            throw new Exception('Desired path not found in Medium API response');
        }


        if (isset($response[0]['data']['tagFromSlug']['sortedFeed']['edges']) && !empty($response[0]['data']['tagFromSlug']['sortedFeed']['edges'])) {
            $edges = $response[0]['data']['tagFromSlug']['sortedFeed']['edges'];
        } else {
            throw new Exception('No posts found for the tag: ' . $tag);
        }

        //loop and return array of links
        $articles = [];

        //reset cursor for pagination
        $this->cursor = '';

        foreach ($edges as $edge) {
            $articles[] = $edge['node']['mediumUrl'];
            $this->cursor = $edge['cursor']; //get the cursor for pagination
        }

        //report number of articles found
        echo '<br>Found ' . count($articles) . ' articles for tag: ' . $tag . ' on page: ' . $page;

        return $articles;
    }

    //fetchByUser
    public function fetchByUser(string $user, string $start = '')
    {

        //correct user format
        $user = $this->correctUser($user);

        echo '<br>Fetching articles by user: ' . $user;
        echo '<br>Using cursor: ' . $start;

        //sample json
        $sample_json = '[{"operationName":"UserProfileQuery","variables":{"homepagePostsFrom":"","includeDistributedResponses":true,"includeShouldFollowPostForExternalSearch":true,"id":"","username":"","homepagePostsLimit":10},"query":"query UserProfileQuery($id: ID, $username: ID, $homepagePostsLimit: PaginationLimit, $homepagePostsFrom: String = null, $includeDistributedResponses: Boolean = true, $includeShouldFollowPostForExternalSearch: Boolean = false) {\n  userResult(id: $id, username: $username) {\n    __typename\n    ... on User {\n      id\n      name\n      viewerIsUser\n      viewerEdge {\n        id\n        isFollowing\n        __typename\n      }\n      homePostsPublished: homepagePostsConnection(paging: {limit: 1}) {\n        posts {\n          id\n          __typename\n        }\n        __typename\n      }\n      ...UserCanonicalizer_user\n      ...MastodonVerificationLink_user\n      ...UserProfileScreen_user\n      __typename\n    }\n  }\n}\n\nfragment getHexFromColorValue_colorValue on ColorValue {\n  rgb\n  alpha\n  __typename\n}\n\nfragment getOpaqueHexFromColorValue_colorValue on ColorValue {\n  rgb\n  __typename\n}\n\nfragment PublisherHeaderBackground_customStyleSheet on CustomStyleSheet {\n  id\n  global {\n    colorPalette {\n      background {\n        rgb\n        __typename\n      }\n      __typename\n    }\n    __typename\n  }\n  header {\n    headerScale\n    backgroundImageDisplayMode\n    backgroundImageVerticalAlignment\n    backgroundColorDisplayMode\n    backgroundColor {\n      alpha\n      rgb\n      ...getHexFromColorValue_colorValue\n      ...getOpaqueHexFromColorValue_colorValue\n      __typename\n    }\n    secondaryBackgroundColor {\n      ...getHexFromColorValue_colorValue\n      __typename\n    }\n    postBackgroundColor {\n      ...getHexFromColorValue_colorValue\n      __typename\n    }\n    backgroundImage {\n      id\n      originalWidth\n      __typename\n    }\n    __typename\n  }\n  __typename\n}\n\nfragment ThemeUtil_colorPoint on ColorPoint {\n  color\n  point\n  __typename\n}\n\nfragment ThemeUtilInterpolateHelpers_colorSpectrum on ColorSpectrum {\n  colorPoints {\n    ...ThemeUtil_colorPoint\n    __typename\n  }\n  __typename\n}\n\nfragment ThemeUtil_colorSpectrum on ColorSpectrum {\n  backgroundColor\n  ...ThemeUtilInterpolateHelpers_colorSpectrum\n  __typename\n}\n\nfragment customTintBackgroundTheme_colorPalette on ColorPalette {\n  tintBackgroundSpectrum {\n    ...ThemeUtil_colorSpectrum\n    __typename\n  }\n  __typename\n}\n\nfragment collectionTintBackgroundTheme_colorPalette on ColorPalette {\n  ...customTintBackgroundTheme_colorPalette\n  __typename\n}\n\nfragment customTintBackgroundTheme_customStyleSheet on CustomStyleSheet {\n  id\n  global {\n    colorPalette {\n      primary {\n        colorPalette {\n          ...customTintBackgroundTheme_colorPalette\n          __typename\n        }\n        __typename\n      }\n      __typename\n    }\n    __typename\n  }\n  __typename\n}\n\nfragment collectionTintBackgroundTheme_customStyleSheet on CustomStyleSheet {\n  id\n  ...customTintBackgroundTheme_customStyleSheet\n  __typename\n}\n\nfragment collectionTintBackgroundTheme_collection on Collection {\n  colorPalette {\n    ...collectionTintBackgroundTheme_colorPalette\n    __typename\n  }\n  customStyleSheet {\n    id\n    ...collectionTintBackgroundTheme_customStyleSheet\n    __typename\n  }\n  __typename\n  id\n}\n\nfragment collectionUrl_collection on Collection {\n  id\n  domain\n  slug\n  __typename\n}\n\nfragment userUrl_user on User {\n  __typename\n  id\n  customDomainState {\n    live {\n      domain\n      __typename\n    }\n    __typename\n  }\n  hasSubdomain\n  username\n}\n\nfragment publisherUrl_publisher on Publisher {\n  id\n  __typename\n  ... on Collection {\n    ...collectionUrl_collection\n    __typename\n    id\n  }\n  ... on User {\n    ...userUrl_user\n    __typename\n    id\n  }\n}\n\nfragment PublisherHeaderBackground_publisher on Publisher {\n  __typename\n  id\n  customStyleSheet {\n    ...PublisherHeaderBackground_customStyleSheet\n    __typename\n    id\n  }\n  ... on Collection {\n    colorPalette {\n      tintBackgroundSpectrum {\n        backgroundColor\n        __typename\n      }\n      __typename\n    }\n    isAuroraVisible\n    legacyHeaderBackgroundImage {\n      id\n      originalWidth\n      focusPercentX\n      focusPercentY\n      __typename\n    }\n    ...collectionTintBackgroundTheme_collection\n    __typename\n    id\n  }\n  ...publisherUrl_publisher\n}\n\nfragment CollectionAvatar_collection on Collection {\n  name\n  avatar {\n    id\n    __typename\n  }\n  ...collectionUrl_collection\n  __typename\n  id\n}\n\nfragment UserAvatar_user on User {\n  __typename\n  id\n  imageId\n  membership {\n    tier\n    __typename\n    id\n  }\n  name\n  username\n  ...userUrl_user\n}\n\nfragment PublisherAvatar_publisher on Publisher {\n  __typename\n  ... on Collection {\n    id\n    ...CollectionAvatar_collection\n    __typename\n  }\n  ... on User {\n    id\n    ...UserAvatar_user\n    __typename\n  }\n}\n\nfragment PublisherHeaderLogo_publisher on Publisher {\n  __typename\n  id\n  name\n  ... on Collection {\n    logo {\n      id\n      __typename\n    }\n    __typename\n    id\n  }\n}\n\nfragment isUserVerifiedBookAuthor_user on User {\n  verifications {\n    isBookAuthor\n    __typename\n  }\n  __typename\n  id\n}\n\nfragment UserPronouns_user on User {\n  pronouns\n  __typename\n  id\n}\n\nfragment PublisherHeaderName_publisher on Publisher {\n  __typename\n  id\n  customStyleSheet {\n    id\n    header {\n      appNameColor {\n        ...getHexFromColorValue_colorValue\n        __typename\n      }\n      __typename\n    }\n    __typename\n  }\n  name\n  ... on User {\n    ...isUserVerifiedBookAuthor_user\n    ...UserPronouns_user\n    __typename\n    id\n  }\n}\n\nfragment PublisherFollowersCount_publisher on Publisher {\n  id\n  __typename\n  id\n  ... on Collection {\n    slug\n    subscriberCount\n    ...collectionUrl_collection\n    __typename\n    id\n  }\n  ... on User {\n    socialStats {\n      followerCount\n      __typename\n    }\n    username\n    ...userUrl_user\n    __typename\n    id\n  }\n}\n\nfragment useLogo_imageMetadata on ImageMetadata {\n  __typename\n  id\n  originalHeight\n  originalWidth\n}\n\nfragment useLogo_publisher on Publisher {\n  __typename\n  id\n  customStyleSheet {\n    id\n    header {\n      logoImage {\n        ...useLogo_imageMetadata\n        __typename\n      }\n      appNameTreatment\n      __typename\n    }\n    __typename\n  }\n  name\n  ... on Collection {\n    isAuroraVisible\n    logo {\n      ...useLogo_imageMetadata\n      __typename\n      id\n    }\n    __typename\n    id\n  }\n}\n\nfragment PublisherHeaderNameplate_publisher on Publisher {\n  ...PublisherAvatar_publisher\n  ...PublisherHeaderLogo_publisher\n  ...PublisherHeaderName_publisher\n  ...PublisherFollowersCount_publisher\n  ...useLogo_publisher\n  __typename\n}\n\nfragment MutePopoverOptions_collection on Collection {\n  id\n  __typename\n}\n\nfragment MetaHeaderPubMenu_publisher_collection on Collection {\n  id\n  slug\n  name\n  domain\n  newsletterV3 {\n    slug\n    __typename\n    id\n  }\n  ...MutePopoverOptions_collection\n  __typename\n}\n\nfragment MutePopoverOptions_creator on User {\n  id\n  __typename\n}\n\nfragment MetaHeaderPubMenu_publisher_user on User {\n  id\n  username\n  ...MutePopoverOptions_creator\n  __typename\n}\n\nfragment MetaHeaderPubMenu_publisher on Publisher {\n  __typename\n  ... on Collection {\n    ...MetaHeaderPubMenu_publisher_collection\n    __typename\n    id\n  }\n  ... on User {\n    ...MetaHeaderPubMenu_publisher_user\n    __typename\n    id\n  }\n}\n\nfragment PublisherHeaderMenu_publisher on Publisher {\n  __typename\n  ...MetaHeaderPubMenu_publisher\n}\n\nfragment SignInOptions_collection on Collection {\n  id\n  name\n  __typename\n}\n\nfragment SignUpOptions_collection on Collection {\n  id\n  name\n  __typename\n}\n\nfragment SusiModal_collection on Collection {\n  name\n  ...SignInOptions_collection\n  ...SignUpOptions_collection\n  __typename\n  id\n}\n\nfragment SusiClickable_collection on Collection {\n  ...SusiModal_collection\n  __typename\n  id\n}\n\nfragment CollectionFollowButton_collection on Collection {\n  __typename\n  id\n  slug\n  name\n  ...SusiClickable_collection\n}\n\nfragment SignInOptions_user on User {\n  id\n  name\n  imageId\n  __typename\n}\n\nfragment SignUpOptions_user on User {\n  id\n  name\n  imageId\n  __typename\n}\n\nfragment SusiModal_user on User {\n  ...SignInOptions_user\n  ...SignUpOptions_user\n  __typename\n  id\n}\n\nfragment useNewsletterV3Subscription_newsletterV3 on NewsletterV3 {\n  id\n  type\n  slug\n  name\n  collection {\n    slug\n    __typename\n    id\n  }\n  user {\n    id\n    name\n    username\n    newsletterV3 {\n      id\n      __typename\n    }\n    __typename\n  }\n  __typename\n}\n\nfragment useNewsletterV3Subscription_user on User {\n  id\n  username\n  newsletterV3 {\n    ...useNewsletterV3Subscription_newsletterV3\n    __typename\n    id\n  }\n  __typename\n}\n\nfragment useAuthorFollowSubscribeButton_user on User {\n  id\n  name\n  ...useNewsletterV3Subscription_user\n  __typename\n}\n\nfragment useAuthorFollowSubscribeButton_newsletterV3 on NewsletterV3 {\n  id\n  name\n  ...useNewsletterV3Subscription_newsletterV3\n  __typename\n}\n\nfragment AuthorFollowSubscribeButton_user on User {\n  id\n  name\n  imageId\n  ...SusiModal_user\n  ...useAuthorFollowSubscribeButton_user\n  newsletterV3 {\n    id\n    ...useAuthorFollowSubscribeButton_newsletterV3\n    __typename\n  }\n  __typename\n}\n\nfragment PublisherHeaderActions_publisher on Publisher {\n  __typename\n  ...PublisherHeaderMenu_publisher\n  ... on Collection {\n    ...CollectionFollowButton_collection\n    __typename\n    id\n  }\n  ... on User {\n    ...AuthorFollowSubscribeButton_user\n    __typename\n    id\n  }\n}\n\nfragment PublisherHeaderNavLink_headerNavigationItem on HeaderNavigationItem {\n  href\n  name\n  tags {\n    id\n    normalizedTagSlug\n    __typename\n  }\n  type\n  __typename\n}\n\nfragment PublisherHeaderNavLink_publisher on Publisher {\n  __typename\n  id\n  ... on Collection {\n    slug\n    __typename\n    id\n  }\n}\n\nfragment PublisherHeaderNav_publisher on Publisher {\n  __typename\n  id\n  customStyleSheet {\n    navigation {\n      navItems {\n        name\n        ...PublisherHeaderNavLink_headerNavigationItem\n        __typename\n      }\n      __typename\n    }\n    __typename\n    id\n  }\n  ...PublisherHeaderNavLink_publisher\n  ... on Collection {\n    domain\n    isAuroraVisible\n    slug\n    navItems {\n      tagSlug\n      title\n      url\n      __typename\n    }\n    __typename\n    id\n  }\n  ... on User {\n    customDomainState {\n      live {\n        domain\n        __typename\n      }\n      __typename\n    }\n    hasSubdomain\n    username\n    homePostsPublished: homepagePostsConnection(paging: {limit: 1}) {\n      posts {\n        id\n        __typename\n      }\n      __typename\n    }\n    ...isUserVerifiedBookAuthor_user\n    __typename\n    id\n  }\n}\n\nfragment PublisherHeader_publisher on Publisher {\n  id\n  ...PublisherHeaderBackground_publisher\n  ...PublisherHeaderNameplate_publisher\n  ...PublisherHeaderActions_publisher\n  ...PublisherHeaderNav_publisher\n  ...PublisherHeaderMenu_publisher\n  __typename\n}\n\nfragment StreamPostPreviewImage_imageMetadata on ImageMetadata {\n  id\n  focusPercentX\n  focusPercentY\n  alt\n  __typename\n}\n\nfragment StreamPostPreviewImage_post on Post {\n  title\n  previewImage {\n    ...StreamPostPreviewImage_imageMetadata\n    __typename\n    id\n  }\n  __typename\n  id\n}\n\nfragment SusiClickable_user on User {\n  ...SusiModal_user\n  __typename\n  id\n}\n\nfragment SusiModal_post on Post {\n  id\n  creator {\n    id\n    __typename\n  }\n  __typename\n}\n\nfragment SusiClickable_post on Post {\n  id\n  mediumUrl\n  ...SusiModal_post\n  __typename\n}\n\nfragment MultiVoteCount_post on Post {\n  id\n  __typename\n}\n\nfragment MultiVote_post on Post {\n  id\n  creator {\n    id\n    ...SusiClickable_user\n    __typename\n  }\n  isPublished\n  ...SusiClickable_post\n  collection {\n    id\n    slug\n    __typename\n  }\n  isLimitedState\n  ...MultiVoteCount_post\n  __typename\n}\n\nfragment PostPreviewFooterSocial_post on Post {\n  id\n  ...MultiVote_post\n  allowResponses\n  isPublished\n  isLimitedState\n  postResponses {\n    count\n    __typename\n  }\n  __typename\n}\n\nfragment AddToCatalogBase_post on Post {\n  id\n  isPublished\n  ...SusiClickable_post\n  __typename\n}\n\nfragment AddToCatalogBookmarkButton_post on Post {\n  ...AddToCatalogBase_post\n  __typename\n  id\n}\n\nfragment BookmarkButton_post on Post {\n  visibility\n  ...SusiClickable_post\n  ...AddToCatalogBookmarkButton_post\n  __typename\n  id\n}\n\nfragment FollowMenuOptions_user on User {\n  id\n  ...AuthorFollowSubscribeButton_user\n  __typename\n}\n\nfragment FollowMenuOptions_collection on Collection {\n  id\n  name\n  __typename\n}\n\nfragment ClapMutation_post on Post {\n  __typename\n  id\n  clapCount\n  ...MultiVoteCount_post\n}\n\nfragment OverflowMenuItemUndoClaps_post on Post {\n  id\n  clapCount\n  ...ClapMutation_post\n  __typename\n}\n\nfragment NegativeSignalModal_publisher on Publisher {\n  __typename\n  id\n  name\n}\n\nfragment NegativeSignalModal_post on Post {\n  id\n  creator {\n    ...NegativeSignalModal_publisher\n    viewerEdge {\n      id\n      isMuting\n      __typename\n    }\n    __typename\n    id\n  }\n  collection {\n    ...NegativeSignalModal_publisher\n    viewerEdge {\n      id\n      isMuting\n      __typename\n    }\n    __typename\n    id\n  }\n  __typename\n}\n\nfragment ExplicitSignalMenuOptions_post on Post {\n  ...NegativeSignalModal_post\n  __typename\n  id\n}\n\nfragment OverflowMenu_post on Post {\n  id\n  creator {\n    id\n    ...FollowMenuOptions_user\n    __typename\n  }\n  collection {\n    id\n    ...FollowMenuOptions_collection\n    __typename\n  }\n  ...OverflowMenuItemUndoClaps_post\n  ...AddToCatalogBase_post\n  ...ExplicitSignalMenuOptions_post\n  __typename\n}\n\nfragment OverflowMenuButton_post on Post {\n  id\n  visibility\n  ...OverflowMenu_post\n  __typename\n}\n\nfragment PostPreviewFooterMenu_post on Post {\n  id\n  ...BookmarkButton_post\n  ...OverflowMenuButton_post\n  __typename\n}\n\nfragment usePostPublishedAt_post on Post {\n  firstPublishedAt\n  latestPublishedAt\n  pinnedAt\n  __typename\n  id\n}\n\nfragment Star_post on Post {\n  id\n  creator {\n    id\n    __typename\n  }\n  isLocked\n  __typename\n}\n\nfragment PostPreviewFooterMeta_post on Post {\n  isLocked\n  postResponses {\n    count\n    __typename\n  }\n  ...usePostPublishedAt_post\n  ...Star_post\n  __typename\n  id\n}\n\nfragment PostPreviewFooter_post on Post {\n  ...PostPreviewFooterSocial_post\n  ...PostPreviewFooterMenu_post\n  ...PostPreviewFooterMeta_post\n  __typename\n  id\n}\n\nfragment PostPreviewBylineAuthorAvatar_user on User {\n  ...UserAvatar_user\n  __typename\n  id\n}\n\nfragment UserLink_user on User {\n  ...userUrl_user\n  __typename\n  id\n}\n\nfragment UserName_user on User {\n  id\n  name\n  ...isUserVerifiedBookAuthor_user\n  ...UserLink_user\n  __typename\n}\n\nfragment PostPreviewByLineAuthor_user on User {\n  ...PostPreviewBylineAuthorAvatar_user\n  ...UserName_user\n  __typename\n  id\n}\n\nfragment PublicationFollowButton_collection on Collection {\n  id\n  slug\n  name\n  ...SusiModal_collection\n  __typename\n}\n\nfragment EntityPresentationRankedModulePublishingTracker_entity on RankedModulePublishingEntity {\n  __typename\n  ... on Collection {\n    id\n    __typename\n  }\n  ... on User {\n    id\n    __typename\n  }\n}\n\nfragment CollectionTooltip_collection on Collection {\n  id\n  name\n  slug\n  description\n  subscriberCount\n  customStyleSheet {\n    header {\n      backgroundImage {\n        id\n        __typename\n      }\n      __typename\n    }\n    __typename\n    id\n  }\n  ...CollectionAvatar_collection\n  ...PublicationFollowButton_collection\n  ...EntityPresentationRankedModulePublishingTracker_entity\n  __typename\n}\n\nfragment CollectionLinkWithPopover_collection on Collection {\n  name\n  ...collectionUrl_collection\n  ...CollectionTooltip_collection\n  __typename\n  id\n}\n\nfragment PostPreviewByLineCollection_collection on Collection {\n  ...CollectionAvatar_collection\n  ...CollectionTooltip_collection\n  ...CollectionLinkWithPopover_collection\n  __typename\n  id\n}\n\nfragment PostPreviewByLine_post on Post {\n  creator {\n    ...PostPreviewByLineAuthor_user\n    __typename\n    id\n  }\n  collection {\n    ...PostPreviewByLineCollection_collection\n    __typename\n    id\n  }\n  __typename\n  id\n}\n\nfragment PostPreviewInformation_post on Post {\n  readingTime\n  isLocked\n  ...Star_post\n  ...usePostPublishedAt_post\n  __typename\n  id\n}\n\nfragment StreamPostPreviewContent_post on Post {\n  id\n  title\n  previewImage {\n    id\n    __typename\n  }\n  extendedPreviewContent {\n    subtitle\n    __typename\n  }\n  ...StreamPostPreviewImage_post\n  ...PostPreviewFooter_post\n  ...PostPreviewByLine_post\n  ...PostPreviewInformation_post\n  __typename\n}\n\nfragment PostScrollTracker_post on Post {\n  id\n  collection {\n    id\n    __typename\n  }\n  sequence {\n    sequenceId\n    __typename\n  }\n  __typename\n}\n\nfragment usePostUrl_post on Post {\n  id\n  creator {\n    ...userUrl_user\n    __typename\n    id\n  }\n  collection {\n    id\n    domain\n    slug\n    __typename\n  }\n  isSeries\n  mediumUrl\n  sequence {\n    slug\n    __typename\n  }\n  uniqueSlug\n  __typename\n}\n\nfragment PostPreviewContainer_post on Post {\n  id\n  extendedPreviewContent {\n    isFullContent\n    __typename\n  }\n  visibility\n  pinnedAt\n  ...PostScrollTracker_post\n  ...usePostUrl_post\n  __typename\n}\n\nfragment StreamPostPreview_post on Post {\n  id\n  ...StreamPostPreviewContent_post\n  ...PostPreviewContainer_post\n  __typename\n}\n\nfragment customDefaultBackgroundTheme_colorPalette on ColorPalette {\n  highlightSpectrum {\n    ...ThemeUtil_colorSpectrum\n    __typename\n  }\n  defaultBackgroundSpectrum {\n    ...ThemeUtil_colorSpectrum\n    __typename\n  }\n  tintBackgroundSpectrum {\n    ...ThemeUtil_colorSpectrum\n    __typename\n  }\n  __typename\n}\n\nfragment collectionDefaultBackgroundTheme_colorPalette on ColorPalette {\n  ...customDefaultBackgroundTheme_colorPalette\n  __typename\n}\n\nfragment customDefaultBackgroundTheme_customStyleSheet on CustomStyleSheet {\n  id\n  global {\n    colorPalette {\n      primary {\n        colorPalette {\n          ...customDefaultBackgroundTheme_colorPalette\n          __typename\n        }\n        __typename\n      }\n      background {\n        colorPalette {\n          ...customDefaultBackgroundTheme_colorPalette\n          __typename\n        }\n        __typename\n      }\n      __typename\n    }\n    __typename\n  }\n  __typename\n}\n\nfragment collectionDefaultBackgroundTheme_customStyleSheet on CustomStyleSheet {\n  id\n  ...customDefaultBackgroundTheme_customStyleSheet\n  __typename\n}\n\nfragment collectionDefaultBackgroundTheme_collection on Collection {\n  colorPalette {\n    ...collectionDefaultBackgroundTheme_colorPalette\n    __typename\n  }\n  customStyleSheet {\n    id\n    ...collectionDefaultBackgroundTheme_customStyleSheet\n    __typename\n  }\n  __typename\n  id\n}\n\nfragment SignInOptions_newsletterV3 on NewsletterV3 {\n  id\n  name\n  __typename\n}\n\nfragment SignUpOptions_newsletterV3 on NewsletterV3 {\n  id\n  name\n  __typename\n}\n\nfragment SusiModal_newsletterV3 on NewsletterV3 {\n  ...SignInOptions_newsletterV3\n  ...SignUpOptions_newsletterV3\n  __typename\n  id\n}\n\nfragment SusiClickable_newsletterV3 on NewsletterV3 {\n  ...SusiModal_newsletterV3\n  __typename\n  id\n}\n\nfragment NewsletterV3ConsentDialog_newsletterV3 on NewsletterV3 {\n  id\n  user {\n    id\n    __typename\n  }\n  __typename\n}\n\nfragment NewsletterV3SubscribeButton_newsletterV3 on NewsletterV3 {\n  id\n  name\n  slug\n  type\n  user {\n    id\n    name\n    username\n    ...SusiModal_user\n    __typename\n  }\n  collection {\n    slug\n    ...SusiClickable_collection\n    ...collectionDefaultBackgroundTheme_collection\n    __typename\n    id\n  }\n  ...SusiClickable_newsletterV3\n  ...useNewsletterV3Subscription_newsletterV3\n  ...NewsletterV3ConsentDialog_newsletterV3\n  __typename\n}\n\nfragment NewsletterV3SubscribeByEmail_newsletterV3 on NewsletterV3 {\n  id\n  slug\n  type\n  user {\n    id\n    name\n    username\n    __typename\n  }\n  collection {\n    ...collectionDefaultBackgroundTheme_collection\n    ...collectionUrl_collection\n    __typename\n    id\n  }\n  __typename\n}\n\nfragment NewsletterSubscribeComponent_newsletterV3 on NewsletterV3 {\n  ...NewsletterV3SubscribeButton_newsletterV3\n  ...NewsletterV3SubscribeByEmail_newsletterV3\n  __typename\n  id\n}\n\nfragment NewsletterV3Promo_newsletterV3 on NewsletterV3 {\n  slug\n  name\n  description\n  promoHeadline\n  promoBody\n  ...NewsletterSubscribeComponent_newsletterV3\n  __typename\n  id\n}\n\nfragment NewsletterV3Promo_user on User {\n  id\n  username\n  name\n  viewerEdge {\n    isUser\n    __typename\n    id\n  }\n  newsletterV3 {\n    id\n    ...NewsletterV3Promo_newsletterV3\n    __typename\n  }\n  __typename\n}\n\nfragment NewsletterV3Promo_collection on Collection {\n  id\n  slug\n  domain\n  name\n  newsletterV3 {\n    id\n    ...NewsletterV3Promo_newsletterV3\n    __typename\n  }\n  __typename\n}\n\nfragment NewsletterV3Promo_publisher on Publisher {\n  __typename\n  ... on User {\n    ...NewsletterV3Promo_user\n    __typename\n    id\n  }\n  ... on Collection {\n    ...NewsletterV3Promo_collection\n    __typename\n    id\n  }\n}\n\nfragment useShowAuthorNewsletterV3Promo_user on User {\n  id\n  username\n  newsletterV3 {\n    id\n    showPromo\n    slug\n    __typename\n  }\n  __typename\n}\n\nfragment PublisherHomepagePosts_user on User {\n  id\n  ...useShowAuthorNewsletterV3Promo_user\n  __typename\n}\n\nfragment PublisherHomepagePosts_publisher on Publisher {\n  __typename\n  id\n  homepagePostsConnection(\n    paging: {limit: $homepagePostsLimit, from: $homepagePostsFrom}\n    includeDistributedResponses: $includeDistributedResponses\n  ) {\n    posts {\n      ...StreamPostPreview_post\n      pinnedByCreatorAt\n      pinnedAt\n      viewerEdge @include(if: $includeShouldFollowPostForExternalSearch) {\n        id\n        shouldFollowPostForExternalSearch\n        __typename\n      }\n      __typename\n    }\n    pagingInfo {\n      next {\n        from\n        limit\n        __typename\n      }\n      __typename\n    }\n    __typename\n  }\n  ...NewsletterV3Promo_publisher\n  ...PublisherHomepagePosts_user\n}\n\nfragment UserProfileMetadataHelmet_user on User {\n  username\n  name\n  imageId\n  twitterScreenName\n  navItems {\n    title\n    __typename\n  }\n  seoMetaTags {\n    description\n    robots\n    __typename\n  }\n  __typename\n  id\n}\n\nfragment UserProfileMetadata_user on User {\n  id\n  username\n  name\n  bio\n  socialStats {\n    followerCount\n    followingCount\n    __typename\n  }\n  ...userUrl_user\n  ...UserProfileMetadataHelmet_user\n  __typename\n}\n\nfragment SuspendedBannerLoader_user on User {\n  id\n  isSuspended\n  __typename\n}\n\nfragment useAnalytics_user on User {\n  id\n  imageId\n  name\n  username\n  __typename\n}\n\nfragment BookCover_authorBook on AuthorBook {\n  coverImageId\n  __typename\n}\n\nfragment BookWidget_authorBook on AuthorBook {\n  authors {\n    name\n    user {\n      id\n      __typename\n    }\n    __typename\n  }\n  description\n  title\n  links {\n    title\n    url\n    __typename\n  }\n  publicationDate\n  ...BookCover_authorBook\n  __typename\n}\n\nfragment UserProfileBooks_user on User {\n  username\n  authoredBooks {\n    ...BookWidget_authorBook\n    __typename\n  }\n  __typename\n  id\n}\n\nfragment UserCanonicalizer_user on User {\n  id\n  username\n  hasSubdomain\n  customDomainState {\n    live {\n      domain\n      __typename\n    }\n    __typename\n  }\n  __typename\n}\n\nfragment MastodonVerificationLink_user on User {\n  id\n  linkedAccounts {\n    mastodon {\n      domain\n      username\n      __typename\n      id\n    }\n    __typename\n    id\n  }\n  __typename\n}\n\nfragment UserProfileScreen_user on User {\n  __typename\n  id\n  viewerIsUser\n  ...PublisherHeader_publisher\n  ...PublisherHomepagePosts_publisher\n  ...UserProfileMetadata_user\n  ...SuspendedBannerLoader_user\n  ...useAnalytics_user\n  ...isUserVerifiedBookAuthor_user\n  ...UserProfileBooks_user\n}\n"}]';

        //decode json to be an array so we can modify desired parts
        $arr = json_decode($sample_json, true);

        // 0 -> variables -> username
        $arr[0]['variables']['username'] = $user;

        //homepagePostsFrom
        if (!empty($start)) {
            $arr[0]['variables']['homepagePostsFrom'] = $start;
        } else {
            $arr[0]['variables']['homepagePostsFrom'] = '';
        }

        //encode json
        $json = json_encode($arr);

        curl_setopt($this->ch, CURLOPT_POST, true);
        curl_setopt_array($this->ch, array(
            CURLOPT_URL => 'https://medium.com/_/graphql',
            CURLOPT_POSTFIELDS => $json,
            CURLOPT_HTTPHEADER => array(
                'accept: */*',
                'accept-language: en-US,en;q=0.9,ar;q=0.8',
                'apollographql-client-name: lite',
                'apollographql-client-version: main-20250725-202833-18a1775cf9',
                'content-type: application/json',
                'graphql-operation: UserProfileQuery',
                'medium-frontend-app: lite/main-20250725-202833-18a1775cf9',
                'medium-frontend-path: /@coxenacox',
                'medium-frontend-route: user',
                'origin: https://medium.com',
                'priority: u=1, i',
                'referer: https://medium.com/@coxenacox',
                'sec-ch-ua: "Not)A;Brand";v="8", "Chromium";v="138", "Google Chrome";v="138"',
                'sec-ch-ua-mobile: ?0',
                'sec-ch-ua-platform: "macOS"',
                'sec-fetch-dest: empty',
                'sec-fetch-mode: cors',
                'sec-fetch-site: same-origin',
                'user-agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36',

            ),
        ));

        $response = curl_exec($this->ch);

        $httpCode = curl_getinfo($this->ch, CURLINFO_HTTP_CODE);
        if ($httpCode !== 200) {
            echo '<br>Error: ' . $httpCode;
            throw new Exception('HTTP error: ' . $httpCode);
        }

        //error
        if (curl_errno($this->ch)) {
            throw new Exception('Curl error: ' . curl_error($this->ch));
        }

        //empty response
        if (empty($response)) {
            throw new Exception('Empty response from Medium API');
        }

        //json decode
        $response = json_decode($response, true);



        //if json decode failed
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('JSON decode error: ' . json_last_error_msg());
        }


        //check if desired path exists data userResult homepagePostsConnection posts        
        if (!isset($response[0]['data']['userResult']['homepagePostsConnection']['posts'])) {
            throw new Exception('Desired path not found in Medium API response');
        }


        //posts
        $posts = $response[0]['data']['userResult']['homepagePostsConnection']['posts'];

        //loop
        $articles = [];
        foreach ($posts as $post) {
            $articles[] = $post['mediumUrl'];
        }

        //cursor 
        $nextcursor = $response[0]['data']['userResult']['homepagePostsConnection']['pagingInfo']['next']['from'] ?? '';

        //if next cursor is not empty add it to the articles array
        if (!empty($nextcursor)) {
            $this->cursor = $nextcursor;
        }

        //print next cursor
        echo '<br>Next cursor: ' . $this->cursor;

        //return articles
        return $articles;
    }

    function fetchBySearch(string $search, string $start = ''): array
    {

        //correct search string
        $search = $this->correctSearchString($search);

        echo '<br>Fetching articles by search: ' . $search;

        echo '<br>Using cursor: ' . $start;

        //sample json 
        $sample_json = '[{"operationName":"SearchQuery","variables":{"query":"mohamed salah","pagingOptions":{"limit":10,"page":3},"withUsers":false,"withTags":false,"withPosts":true,"withCollections":false,"withLists":false,"peopleSearchOptions":{"filters":"highQualityUser:true OR writtenByHighQulityUser:true","numericFilters":"peopleType!=2","clickAnalytics":true,"analyticsTags":["web-main-content"]},"postsSearchOptions":{"filters":"writtenByHighQualityUser:true","clickAnalytics":true,"analyticsTags":["web-main-content"]},"publicationsSearchOptions":{"clickAnalytics":true,"analyticsTags":["web-main-content"]},"tagsSearchOptions":{"numericFilters":"postCount>=1","clickAnalytics":true,"analyticsTags":["web-main-content"]},"listsSearchOptions":{"clickAnalytics":true,"analyticsTags":["web-main-content"]},"searchInCollection":false,"collectionDomainOrSlug":"medium.com"},"query":"query SearchQuery($query: String!, $pagingOptions: SearchPagingOptions!, $searchInCollection: Boolean!, $collectionDomainOrSlug: String!, $withUsers: Boolean!, $withTags: Boolean!, $withPosts: Boolean!, $withCollections: Boolean!, $withLists: Boolean!, $peopleSearchOptions: SearchOptions, $postsSearchOptions: SearchOptions, $tagsSearchOptions: SearchOptions, $publicationsSearchOptions: SearchOptions, $listsSearchOptions: SearchOptions) {\\n  search(query: $query) @skip(if: $searchInCollection) {\\n    __typename\\n    ...Search_search\\n  }\\n  searchInCollection(query: $query, domainOrSlug: $collectionDomainOrSlug) @include(if: $searchInCollection) {\\n    __typename\\n    ...Search_search\\n  }\\n}\\n\\nfragment userUrl_user on User {\\n  __typename\\n  id\\n  customDomainState {\\n    live {\\n      domain\\n      __typename\\n    }\\n    __typename\\n  }\\n  hasSubdomain\\n  username\\n}\\n\\nfragment UserAvatar_user on User {\\n  __typename\\n  id\\n  imageId\\n  membership {\\n    tier\\n    __typename\\n    id\\n  }\\n  name\\n  username\\n  ...userUrl_user\\n}\\n\\nfragment isUserVerifiedBookAuthor_user on User {\\n  verifications {\\n    isBookAuthor\\n    __typename\\n  }\\n  __typename\\n  id\\n}\\n\\nfragment SignInOptions_user on User {\\n  id\\n  name\\n  imageId\\n  __typename\\n}\\n\\nfragment SignUpOptions_user on User {\\n  id\\n  name\\n  imageId\\n  __typename\\n}\\n\\nfragment SusiModal_user on User {\\n  ...SignInOptions_user\\n  ...SignUpOptions_user\\n  __typename\\n  id\\n}\\n\\nfragment useNewsletterV3Subscription_newsletterV3 on NewsletterV3 {\\n  id\\n  type\\n  slug\\n  name\\n  collection {\\n    slug\\n    __typename\\n    id\\n  }\\n  user {\\n    id\\n    name\\n    username\\n    newsletterV3 {\\n      id\\n      __typename\\n    }\\n    __typename\\n  }\\n  __typename\\n}\\n\\nfragment useNewsletterV3Subscription_user on User {\\n  id\\n  username\\n  newsletterV3 {\\n    ...useNewsletterV3Subscription_newsletterV3\\n    __typename\\n    id\\n  }\\n  __typename\\n}\\n\\nfragment useAuthorFollowSubscribeButton_user on User {\\n  id\\n  name\\n  ...useNewsletterV3Subscription_user\\n  __typename\\n}\\n\\nfragment useAuthorFollowSubscribeButton_newsletterV3 on NewsletterV3 {\\n  id\\n  name\\n  ...useNewsletterV3Subscription_newsletterV3\\n  __typename\\n}\\n\\nfragment AuthorFollowSubscribeButton_user on User {\\n  id\\n  name\\n  imageId\\n  ...SusiModal_user\\n  ...useAuthorFollowSubscribeButton_user\\n  newsletterV3 {\\n    id\\n    ...useAuthorFollowSubscribeButton_newsletterV3\\n    __typename\\n  }\\n  __typename\\n}\\n\\nfragment UserFollowInline_user on User {\\n  id\\n  name\\n  bio\\n  mediumMemberAt\\n  ...UserAvatar_user\\n  ...userUrl_user\\n  ...isUserVerifiedBookAuthor_user\\n  ...AuthorFollowSubscribeButton_user\\n  __typename\\n}\\n\\nfragment SearchPeople_people on SearchPeople {\\n  items {\\n    __typename\\n    ... on User {\\n      algoliaObjectId\\n      __typename\\n      id\\n    }\\n    ...UserFollowInline_user\\n  }\\n  queryId\\n  __typename\\n}\\n\\nfragment TopicPill_tag on Tag {\\n  __typename\\n  id\\n  displayTitle\\n  normalizedTagSlug\\n}\\n\\nfragment SearchTags_tags on SearchTag {\\n  items {\\n    id\\n    algoliaObjectId\\n    ...TopicPill_tag\\n    __typename\\n  }\\n  queryId\\n  __typename\\n}\\n\\nfragment StreamPostPreviewImage_imageMetadata on ImageMetadata {\\n  id\\n  focusPercentX\\n  focusPercentY\\n  alt\\n  __typename\\n}\\n\\nfragment StreamPostPreviewImage_post on Post {\\n  title\\n  previewImage {\\n    ...StreamPostPreviewImage_imageMetadata\\n    __typename\\n    id\\n  }\\n  __typename\\n  id\\n}\\n\\nfragment SusiClickable_user on User {\\n  ...SusiModal_user\\n  __typename\\n  id\\n}\\n\\nfragment SusiModal_post on Post {\\n  id\\n  creator {\\n    id\\n    __typename\\n  }\\n  __typename\\n}\\n\\nfragment SusiClickable_post on Post {\\n  id\\n  mediumUrl\\n  ...SusiModal_post\\n  __typename\\n}\\n\\nfragment MultiVoteCount_post on Post {\\n  id\\n  __typename\\n}\\n\\nfragment MultiVote_post on Post {\\n  id\\n  creator {\\n    id\\n    ...SusiClickable_user\\n    __typename\\n  }\\n  isPublished\\n  ...SusiClickable_post\\n  collection {\\n    id\\n    slug\\n    __typename\\n  }\\n  isLimitedState\\n  ...MultiVoteCount_post\\n  __typename\\n}\\n\\nfragment PostPreviewFooterSocial_post on Post {\\n  id\\n  ...MultiVote_post\\n  allowResponses\\n  isPublished\\n  isLimitedState\\n  postResponses {\\n    count\\n    __typename\\n  }\\n  __typename\\n}\\n\\nfragment AddToCatalogBase_post on Post {\\n  id\\n  isPublished\\n  ...SusiClickable_post\\n  __typename\\n}\\n\\nfragment AddToCatalogBookmarkButton_post on Post {\\n  ...AddToCatalogBase_post\\n  __typename\\n  id\\n}\\n\\nfragment BookmarkButton_post on Post {\\n  visibility\\n  ...SusiClickable_post\\n  ...AddToCatalogBookmarkButton_post\\n  __typename\\n  id\\n}\\n\\nfragment FollowMenuOptions_user on User {\\n  id\\n  ...AuthorFollowSubscribeButton_user\\n  __typename\\n}\\n\\nfragment FollowMenuOptions_collection on Collection {\\n  id\\n  name\\n  __typename\\n}\\n\\nfragment ClapMutation_post on Post {\\n  __typename\\n  id\\n  clapCount\\n  ...MultiVoteCount_post\\n}\\n\\nfragment OverflowMenuItemUndoClaps_post on Post {\\n  id\\n  clapCount\\n  ...ClapMutation_post\\n  __typename\\n}\\n\\nfragment NegativeSignalModal_publisher on Publisher {\\n  __typename\\n  id\\n  name\\n}\\n\\nfragment NegativeSignalModal_post on Post {\\n  id\\n  creator {\\n    ...NegativeSignalModal_publisher\\n    viewerEdge {\\n      id\\n      isMuting\\n      __typename\\n    }\\n    __typename\\n    id\\n  }\\n  collection {\\n    ...NegativeSignalModal_publisher\\n    viewerEdge {\\n      id\\n      isMuting\\n      __typename\\n    }\\n    __typename\\n    id\\n  }\\n  __typename\\n}\\n\\nfragment ExplicitSignalMenuOptions_post on Post {\\n  ...NegativeSignalModal_post\\n  __typename\\n  id\\n}\\n\\nfragment OverflowMenu_post on Post {\\n  id\\n  creator {\\n    id\\n    ...FollowMenuOptions_user\\n    __typename\\n  }\\n  collection {\\n    id\\n    ...FollowMenuOptions_collection\\n    __typename\\n  }\\n  ...OverflowMenuItemUndoClaps_post\\n  ...AddToCatalogBase_post\\n  ...ExplicitSignalMenuOptions_post\\n  __typename\\n}\\n\\nfragment OverflowMenuButton_post on Post {\\n  id\\n  visibility\\n  ...OverflowMenu_post\\n  __typename\\n}\\n\\nfragment PostPreviewFooterMenu_post on Post {\\n  id\\n  ...BookmarkButton_post\\n  ...OverflowMenuButton_post\\n  __typename\\n}\\n\\nfragment usePostPublishedAt_post on Post {\\n  firstPublishedAt\\n  latestPublishedAt\\n  pinnedAt\\n  __typename\\n  id\\n}\\n\\nfragment Star_post on Post {\\n  id\\n  creator {\\n    id\\n    __typename\\n  }\\n  isLocked\\n  __typename\\n}\\n\\nfragment PostPreviewFooterMeta_post on Post {\\n  isLocked\\n  postResponses {\\n    count\\n    __typename\\n  }\\n  ...usePostPublishedAt_post\\n  ...Star_post\\n  __typename\\n  id\\n}\\n\\nfragment PostPreviewFooter_post on Post {\\n  ...PostPreviewFooterSocial_post\\n  ...PostPreviewFooterMenu_post\\n  ...PostPreviewFooterMeta_post\\n  __typename\\n  id\\n}\\n\\nfragment PostPreviewBylineAuthorAvatar_user on User {\\n  ...UserAvatar_user\\n  __typename\\n  id\\n}\\n\\nfragment UserLink_user on User {\\n  ...userUrl_user\\n  __typename\\n  id\\n}\\n\\nfragment UserName_user on User {\\n  id\\n  name\\n  ...isUserVerifiedBookAuthor_user\\n  ...UserLink_user\\n  __typename\\n}\\n\\nfragment PostPreviewByLineAuthor_user on User {\\n  ...PostPreviewBylineAuthorAvatar_user\\n  ...UserName_user\\n  __typename\\n  id\\n}\\n\\nfragment collectionUrl_collection on Collection {\\n  id\\n  domain\\n  slug\\n  __typename\\n}\\n\\nfragment CollectionAvatar_collection on Collection {\\n  name\\n  avatar {\\n    id\\n    __typename\\n  }\\n  ...collectionUrl_collection\\n  __typename\\n  id\\n}\\n\\nfragment SignInOptions_collection on Collection {\\n  id\\n  name\\n  __typename\\n}\\n\\nfragment SignUpOptions_collection on Collection {\\n  id\\n  name\\n  __typename\\n}\\n\\nfragment SusiModal_collection on Collection {\\n  name\\n  ...SignInOptions_collection\\n  ...SignUpOptions_collection\\n  __typename\\n  id\\n}\\n\\nfragment PublicationFollowButton_collection on Collection {\\n  id\\n  slug\\n  name\\n  ...SusiModal_collection\\n  __typename\\n}\\n\\nfragment EntityPresentationRankedModulePublishingTracker_entity on RankedModulePublishingEntity {\\n  __typename\\n  ... on Collection {\\n    id\\n    __typename\\n  }\\n  ... on User {\\n    id\\n    __typename\\n  }\\n}\\n\\nfragment CollectionTooltip_collection on Collection {\\n  id\\n  name\\n  slug\\n  description\\n  subscriberCount\\n  customStyleSheet {\\n    header {\\n      backgroundImage {\\n        id\\n        __typename\\n      }\\n      __typename\\n    }\\n    __typename\\n    id\\n  }\\n  ...CollectionAvatar_collection\\n  ...PublicationFollowButton_collection\\n  ...EntityPresentationRankedModulePublishingTracker_entity\\n  __typename\\n}\\n\\nfragment CollectionLinkWithPopover_collection on Collection {\\n  name\\n  ...collectionUrl_collection\\n  ...CollectionTooltip_collection\\n  __typename\\n  id\\n}\\n\\nfragment PostPreviewByLineCollection_collection on Collection {\\n  ...CollectionAvatar_collection\\n  ...CollectionTooltip_collection\\n  ...CollectionLinkWithPopover_collection\\n  __typename\\n  id\\n}\\n\\nfragment PostPreviewByLine_post on Post {\\n  creator {\\n    ...PostPreviewByLineAuthor_user\\n    __typename\\n    id\\n  }\\n  collection {\\n    ...PostPreviewByLineCollection_collection\\n    __typename\\n    id\\n  }\\n  __typename\\n  id\\n}\\n\\nfragment PostPreviewInformation_post on Post {\\n  readingTime\\n  isLocked\\n  ...Star_post\\n  ...usePostPublishedAt_post\\n  __typename\\n  id\\n}\\n\\nfragment StreamPostPreviewContent_post on Post {\\n  id\\n  title\\n  previewImage {\\n    id\\n    __typename\\n  }\\n  extendedPreviewContent {\\n    subtitle\\n    __typename\\n  }\\n  ...StreamPostPreviewImage_post\\n  ...PostPreviewFooter_post\\n  ...PostPreviewByLine_post\\n  ...PostPreviewInformation_post\\n  __typename\\n}\\n\\nfragment PostScrollTracker_post on Post {\\n  id\\n  collection {\\n    id\\n    __typename\\n  }\\n  sequence {\\n    sequenceId\\n    __typename\\n  }\\n  __typename\\n}\\n\\nfragment usePostUrl_post on Post {\\n  id\\n  creator {\\n    ...userUrl_user\\n    __typename\\n    id\\n  }\\n  collection {\\n    id\\n    domain\\n    slug\\n    __typename\\n  }\\n  isSeries\\n  mediumUrl\\n  sequence {\\n    slug\\n    __typename\\n  }\\n  uniqueSlug\\n  __typename\\n}\\n\\nfragment PostPreviewContainer_post on Post {\\n  id\\n  extendedPreviewContent {\\n    isFullContent\\n    __typename\\n  }\\n  visibility\\n  pinnedAt\\n  ...PostScrollTracker_post\\n  ...usePostUrl_post\\n  __typename\\n}\\n\\nfragment StreamPostPreview_post on Post {\\n  id\\n  ...StreamPostPreviewContent_post\\n  ...PostPreviewContainer_post\\n  __typename\\n}\\n\\nfragment SearchPosts_posts on SearchPost {\\n  items {\\n    id\\n    algoliaObjectId\\n    ...StreamPostPreview_post\\n    __typename\\n  }\\n  queryId\\n  __typename\\n}\\n\\nfragment CollectionFollowInline_collection on Collection {\\n  __typename\\n  id\\n  name\\n  domain\\n  shortDescription\\n  slug\\n  ...CollectionAvatar_collection\\n  ...PublicationFollowButton_collection\\n}\\n\\nfragment usePublicationSearchResultClickTracker_collection on Collection {\\n  id\\n  algoliaObjectId\\n  domain\\n  slug\\n  __typename\\n}\\n\\nfragment SearchCollections_collection on Collection {\\n  id\\n  ...CollectionFollowInline_collection\\n  ...usePublicationSearchResultClickTracker_collection\\n  __typename\\n}\\n\\nfragment SearchCollections_collections on SearchCollection {\\n  items {\\n    ...SearchCollections_collection\\n    __typename\\n  }\\n  queryId\\n  __typename\\n}\\n\\nfragment getCatalogSlugId_Catalog on Catalog {\\n  id\\n  name\\n  __typename\\n}\\n\\nfragment formatItemsCount_catalog on Catalog {\\n  postItemsCount\\n  __typename\\n  id\\n}\\n\\nfragment PreviewCatalogCovers_catalogItemV2 on CatalogItemV2 {\\n  catalogItemId\\n  entity {\\n    __typename\\n    ... on Post {\\n      visibility\\n      previewImage {\\n        id\\n        alt\\n        __typename\\n      }\\n      __typename\\n      id\\n    }\\n  }\\n  __typename\\n}\\n\\nfragment CatalogsListItemCovers_catalog on Catalog {\\n  listItemsConnection: itemsConnection(pagingOptions: {limit: 10}) {\\n    items {\\n      catalogItemId\\n      ...PreviewCatalogCovers_catalogItemV2\\n      __typename\\n    }\\n    __typename\\n  }\\n  __typename\\n  id\\n}\\n\\nfragment catalogUrl_catalog on Catalog {\\n  id\\n  predefined\\n  ...getCatalogSlugId_Catalog\\n  creator {\\n    ...userUrl_user\\n    __typename\\n    id\\n  }\\n  __typename\\n}\\n\\nfragment CatalogContentNonCreatorMenu_catalog on Catalog {\\n  id\\n  viewerEdge {\\n    clapCount\\n    __typename\\n    id\\n  }\\n  ...catalogUrl_catalog\\n  __typename\\n}\\n\\nfragment UpdateCatalogDialog_catalog on Catalog {\\n  id\\n  name\\n  description\\n  visibility\\n  type\\n  __typename\\n}\\n\\nfragment CatalogContentCreatorMenu_catalog on Catalog {\\n  id\\n  visibility\\n  name\\n  description\\n  type\\n  postItemsCount\\n  predefined\\n  disallowResponses\\n  creator {\\n    ...userUrl_user\\n    __typename\\n    id\\n  }\\n  ...UpdateCatalogDialog_catalog\\n  ...catalogUrl_catalog\\n  __typename\\n}\\n\\nfragment CatalogContentMenu_catalog on Catalog {\\n  creator {\\n    ...userUrl_user\\n    __typename\\n    id\\n  }\\n  ...CatalogContentNonCreatorMenu_catalog\\n  ...CatalogContentCreatorMenu_catalog\\n  __typename\\n  id\\n}\\n\\nfragment SaveCatalogButton_catalog on Catalog {\\n  id\\n  creator {\\n    id\\n    username\\n    __typename\\n  }\\n  viewerEdge {\\n    id\\n    isFollowing\\n    __typename\\n  }\\n  ...getCatalogSlugId_Catalog\\n  __typename\\n}\\n\\nfragment CatalogsListItem_catalog on Catalog {\\n  id\\n  name\\n  predefined\\n  visibility\\n  creator {\\n    imageId\\n    name\\n    ...userUrl_user\\n    ...isUserVerifiedBookAuthor_user\\n    __typename\\n    id\\n  }\\n  ...getCatalogSlugId_Catalog\\n  ...formatItemsCount_catalog\\n  ...CatalogsListItemCovers_catalog\\n  ...CatalogContentMenu_catalog\\n  ...SaveCatalogButton_catalog\\n  __typename\\n}\\n\\nfragment SearchLists_catalogs on SearchCatalog {\\n  items {\\n    id\\n    algoliaObjectId\\n    ...CatalogsListItem_catalog\\n    __typename\\n  }\\n  queryId\\n  __typename\\n}\\n\\nfragment Search_search on Search {\\n  people(pagingOptions: $pagingOptions, algoliaOptions: $peopleSearchOptions) @include(if: $withUsers) {\\n    ... on SearchPeople {\\n      pagingInfo {\\n        next {\\n          limit\\n          page\\n          __typename\\n        }\\n        __typename\\n      }\\n      ...SearchPeople_people\\n      __typename\\n    }\\n    __typename\\n  }\\n  tags(pagingOptions: $pagingOptions, algoliaOptions: $tagsSearchOptions) @include(if: $withTags) {\\n    ... on SearchTag {\\n      pagingInfo {\\n        next {\\n          limit\\n          page\\n          __typename\\n        }\\n        __typename\\n      }\\n      ...SearchTags_tags\\n      __typename\\n    }\\n    __typename\\n  }\\n  posts(pagingOptions: $pagingOptions, algoliaOptions: $postsSearchOptions) @include(if: $withPosts) {\\n    ... on SearchPost {\\n      pagingInfo {\\n        next {\\n          limit\\n          page\\n          __typename\\n        }\\n        __typename\\n      }\\n      ...SearchPosts_posts\\n      __typename\\n    }\\n    __typename\\n  }\\n  collections(\\n    pagingOptions: $pagingOptions\\n    algoliaOptions: $publicationsSearchOptions\\n  ) @include(if: $withCollections) {\\n    ... on SearchCollection {\\n      pagingInfo {\\n        next {\\n          limit\\n          page\\n          __typename\\n        }\\n        __typename\\n      }\\n      ...SearchCollections_collections\\n      __typename\\n    }\\n    __typename\\n  }\\n  catalogs(pagingOptions: $pagingOptions, algoliaOptions: $listsSearchOptions) @include(if: $withLists) {\\n    ... on SearchCatalog {\\n      pagingInfo {\\n        next {\\n          limit\\n          page\\n          __typename\\n        }\\n        __typename\\n      }\\n      ...SearchLists_catalogs\\n      __typename\\n    }\\n    __typename\\n  }\\n  __typename\\n}\\n"}]';


        //decode json to be an array so we can modify desired parts
        $arr = json_decode($sample_json, true);

        //print
       // print_r($arr);

        // 0 -> variables -> query
        $arr[0]['variables']['query'] = $search;
        
        // 0 -> variables -> pagingOptions ->page
        if (!empty($start)) {

            //convert to int
            $start = (int)$start;

            $arr[0]['variables']['pagingOptions']['page'] = $start;
        } else {
            $arr[0]['variables']['pagingOptions']['page'] = 0;  
        }
 
        // encode json
        $json = json_encode($arr);

        curl_setopt($this->ch, CURLOPT_POST, true);
        curl_setopt_array($this->ch, array(
            CURLOPT_URL => 'https://medium.com/_/graphql',
            CURLOPT_POSTFIELDS => $json,
            CURLOPT_HTTPHEADER => array(
                'accept: */*',
                'accept-language: en-US,en;q=0.9,ar;q=0.8',
                'apollographql-client-name: lite',
                'apollographql-client-version: main-20250725-202833-18a1775cf9',
                'content-type: application/json',
                'graphql-operation: SearchQuery',
                'medium-frontend-app: lite/main-20250725-202833-18a1775cf9',
                'medium-frontend-path: /search?q=mohamed+salah',
                'medium-frontend-route: search',
                'origin: https://medium.com',
                'priority: u=1, i',
                'referer: https://medium.com/search?q=mohamed+salah',
                'sec-ch-ua: "Not)A;Brand";v="8", "Chromium";v="138", "Google Chrome";v="138"',
                'sec-ch-ua-mobile: ?0',
                'sec-ch-ua-platform: "macOS"',
                'sec-fetch-dest: empty',
                'sec-fetch-mode: cors',
                'sec-fetch-site: same-origin',
                'user-agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36',
            ),
        ));

        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($this->ch);
        $httpCode = curl_getinfo($this->ch, CURLINFO_HTTP_CODE);
 
        if ($httpCode !== 200) {
            echo '<br>Error: ' . $httpCode;
            throw new Exception('HTTP error: ' . $httpCode);
        }

        //error
        if (curl_errno($this->ch)) {
            throw new Exception('Curl error: ' . curl_error($this->ch));
        }

        //empty response
        if (empty($response)) {
            throw new Exception('Empty response from Medium API');
        }

        //json decode
        $response = json_decode($response, true);

        //if json decode failed
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('JSON decode error: ' . json_last_error_msg()); 
        }

        //check if desired path exists data search
        if (!isset($response[0]['data']['search'])) {
            throw new Exception('Desired path not found in Medium API response');
        }

        //posts
        $posts = $response[0]['data']['search']['posts']['items'] ?? [];

        //loop
        $articles = [];
        foreach ($posts as $post) {
            $articles[] = $post['mediumUrl'];
        }

        //cursor
        $nextcursor = $response[0]['data']['search']['posts']['pagingInfo']['next']['page'] ?? '';

        //if next cursor is not empty add it to the articles array
        if (!empty($nextcursor)) {
            $this->cursor = $nextcursor;
        }

        //print next cursor
        echo '<br>Next cursor: ' . $this->cursor;

        //return articles
        return $articles;


    }

    //fuction to correct search string 
    //for example https://medium.com/search?q=mohamed+salah
    // to mohamed salah
    public function correctSearchString(string $search): string
    {
        
        //if contains medium.com, parse the url and get the q parameter 
        if (strpos($search, 'medium.com') !== false) {
            $parsedUrl = parse_url($search);
            parse_str($parsedUrl['query'] ?? '', $queryParams);
            $search = $queryParams['q'] ?? '';
        }

       
        //return the search string
        return trim($search);

    }

    //correct user function which extracts the user from the url
    //user could have added the user as https://medium.com/@thefootballfable
    //or he added it as @thefootballfable
    //or if added just as thefootballfable
    public function correctUser(string $user): string
    {


        //if user contains / remove everything after the last /
        if (strpos($user, '/') !== false) {
            $user = substr($user, strrpos($user, '/') + 1);
        }

        //if contains the domain remove it
        if (strpos($user, 'medium.com') !== false) {
            //explode by / and take the last part
            $parts = explode('/', $user);
            $user = end($parts);
        }

        //remove additional ?or & parameters
        if (strpos($user, '?') !== false) {
            $user = substr($user, 0, strpos($user, '?'));
        }
        if (strpos($user, '&') !== false) {
            $user = substr($user, 0, strpos($user, '&'));
        }

        //remove @ if exists
        if (strpos($user, '@') === 0) {
            $user = substr($user, 1);
        }

        //trim the user
        $user = trim($user);

        return $user;
    }
}
