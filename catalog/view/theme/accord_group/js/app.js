(() => {
    "use strict";
    const modules_flsModules = {};
    function functions_getHash() {
        if (location.hash) return location.hash.replace("#", "");
    }
    function setHash(hash) {
        hash = hash ? `#${hash}` : window.location.href.split("#")[0];
        history.pushState("", "", hash);
    }
    let _slideUp = (target, duration = 500, showmore = 0) => {
        if (!target.classList.contains("_slide")) {
            target.classList.add("_slide");
            target.style.transitionProperty = "height, margin, padding";
            target.style.transitionDuration = duration + "ms";
            target.style.height = `${target.offsetHeight}px`;
            target.offsetHeight;
            target.style.overflow = "hidden";
            target.style.height = showmore ? `${showmore}px` : `0px`;
            target.style.paddingTop = 0;
            target.style.paddingBottom = 0;
            target.style.marginTop = 0;
            target.style.marginBottom = 0;
            window.setTimeout((() => {
                target.hidden = !showmore ? true : false;
                !showmore ? target.style.removeProperty("height") : null;
                target.style.removeProperty("padding-top");
                target.style.removeProperty("padding-bottom");
                target.style.removeProperty("margin-top");
                target.style.removeProperty("margin-bottom");
                !showmore ? target.style.removeProperty("overflow") : null;
                target.style.removeProperty("transition-duration");
                target.style.removeProperty("transition-property");
                target.classList.remove("_slide");
                document.dispatchEvent(new CustomEvent("slideUpDone", {
                    detail: {
                        target
                    }
                }));
            }), duration);
        }
    };
    let _slideDown = (target, duration = 500, showmore = 0) => {
        if (!target.classList.contains("_slide")) {
            target.classList.add("_slide");
            target.hidden = target.hidden ? false : null;
            showmore ? target.style.removeProperty("height") : null;
            let height = target.offsetHeight;
            target.style.overflow = "hidden";
            target.style.height = showmore ? `${showmore}px` : `0px`;
            target.style.paddingTop = 0;
            target.style.paddingBottom = 0;
            target.style.marginTop = 0;
            target.style.marginBottom = 0;
            target.offsetHeight;
            target.style.transitionProperty = "height, margin, padding";
            target.style.transitionDuration = duration + "ms";
            target.style.height = height + "px";
            target.style.removeProperty("padding-top");
            target.style.removeProperty("padding-bottom");
            target.style.removeProperty("margin-top");
            target.style.removeProperty("margin-bottom");
            window.setTimeout((() => {
                target.style.removeProperty("height");
                target.style.removeProperty("overflow");
                target.style.removeProperty("transition-duration");
                target.style.removeProperty("transition-property");
                target.classList.remove("_slide");
                document.dispatchEvent(new CustomEvent("slideDownDone", {
                    detail: {
                        target
                    }
                }));
            }), duration);
        }
    };
    let _slideToggle = (target, duration = 500) => {
        if (target.hidden) return _slideDown(target, duration); else return _slideUp(target, duration);
    };
    let bodyLockStatus = true;
    let bodyLockToggle = (delay = 500) => {
        if (document.documentElement.classList.contains("lock")) bodyUnlock(delay); else bodyLock(delay);
    };
    let bodyUnlock = (delay = 500) => {
        if (bodyLockStatus) {
            const lockPaddingElements = document.querySelectorAll("[data-lp]");
            setTimeout((() => {
                lockPaddingElements.forEach((lockPaddingElement => {
                    lockPaddingElement.style.paddingRight = "";
                }));
                document.body.style.paddingRight = "";
                document.documentElement.classList.remove("lock");
            }), delay);
            bodyLockStatus = false;
            setTimeout((function() {
                bodyLockStatus = true;
            }), delay);
        }
    };
    let bodyLock = (delay = 500) => {
        if (bodyLockStatus) {
            const lockPaddingElements = document.querySelectorAll("[data-lp]");
            const lockPaddingValue = window.innerWidth - document.body.offsetWidth + "px";
            lockPaddingElements.forEach((lockPaddingElement => {
                lockPaddingElement.style.paddingRight = lockPaddingValue;
            }));
            document.body.style.paddingRight = lockPaddingValue;
            document.documentElement.classList.add("lock");
            bodyLockStatus = false;
            setTimeout((function() {
                bodyLockStatus = true;
            }), delay);
        }
    };
    function spoilers() {
        const spoilersArray = document.querySelectorAll("[data-spoilers]");
        if (spoilersArray.length > 0) {
            document.addEventListener("click", setspoilerAction);
            const spoilersRegular = Array.from(spoilersArray).filter((function(item, index, self) {
                return !item.dataset.spoilers.split(",")[0];
            }));
            if (spoilersRegular.length) initspoilers(spoilersRegular);
            let mdQueriesArray = dataMediaQueries(spoilersArray, "spoilers");
            if (mdQueriesArray && mdQueriesArray.length) mdQueriesArray.forEach((mdQueriesItem => {
                mdQueriesItem.matchMedia.addEventListener("change", (function() {
                    initspoilers(mdQueriesItem.itemsArray, mdQueriesItem.matchMedia);
                }));
                initspoilers(mdQueriesItem.itemsArray, mdQueriesItem.matchMedia);
            }));
            function initspoilers(spoilersArray, matchMedia = false) {
                spoilersArray.forEach((spoilersBlock => {
                    spoilersBlock = matchMedia ? spoilersBlock.item : spoilersBlock;
                    if (matchMedia.matches || !matchMedia) {
                        spoilersBlock.classList.add("_spoiler-init");
                        initspoilerBody(spoilersBlock);
                    } else {
                        spoilersBlock.classList.remove("_spoiler-init");
                        initspoilerBody(spoilersBlock, false);
                    }
                }));
            }
            function initspoilerBody(spoilersBlock, hidespoilerBody = true) {
                let spoilerItems = spoilersBlock.querySelectorAll("details");
                if (spoilerItems.length) spoilerItems.forEach((spoilerItem => {
                    let spoilerTitle = spoilerItem.querySelector("summary");
                    if (hidespoilerBody) {
                        spoilerTitle.removeAttribute("tabindex");
                        if (!spoilerItem.hasAttribute("data-open")) {
                            spoilerItem.open = false;
                            spoilerTitle.nextElementSibling.hidden = true;
                        } else {
                            spoilerTitle.classList.add("_spoiler-active");
                            spoilerItem.open = true;
                        }
                    } else {
                        spoilerTitle.setAttribute("tabindex", "-1");
                        spoilerTitle.classList.remove("_spoiler-active");
                        spoilerItem.open = true;
                        spoilerTitle.nextElementSibling.hidden = false;
                    }
                }));
            }
            function setspoilerAction(e) {
                const el = e.target;
                if (el.closest("[data-spoiler-close-btn]")) {
                    const spoilerBlock = el.closest("details");
                    const spoilersBlock = el.closest("[data-spoilers]");
                    if (spoilersBlock && spoilersBlock.classList.contains("_spoiler-init")) {
                        const spoilerSpeed = spoilersBlock.dataset.spoilersSpeed ? parseInt(spoilersBlock.dataset.spoilersSpeed) : 500;
                        const spoilerTitle = spoilerBlock.querySelector("summary");
                        spoilerTitle.classList.remove("_spoiler-active");
                        _slideUp(spoilerTitle.nextElementSibling, spoilerSpeed);
                        setTimeout((() => {
                            spoilerBlock.open = false;
                        }), spoilerSpeed);
                        return;
                    }
                }
                if (el.closest("summary") && el.closest("[data-spoilers]")) {
                    e.preventDefault();
                    if (el.closest("[data-spoilers]").classList.contains("_spoiler-init")) {
                        const spoilerTitle = el.closest("summary");
                        const spoilerBlock = spoilerTitle.closest("details");
                        const spoilersBlock = spoilerTitle.closest("[data-spoilers]");
                        const onespoiler = spoilersBlock.hasAttribute("data-one-spoiler");
                        const scrollspoiler = spoilerBlock.hasAttribute("data-spoiler-scroll");
                        const spoilerSpeed = spoilersBlock.dataset.spoilersSpeed ? parseInt(spoilersBlock.dataset.spoilersSpeed) : 500;
                        if (!spoilersBlock.querySelectorAll("._slide").length) {
                            if (onespoiler && !spoilerBlock.open) hidespoilersBody(spoilersBlock);
                            !spoilerBlock.open ? spoilerBlock.open = true : setTimeout((() => {
                                spoilerBlock.open = false;
                            }), spoilerSpeed);
                            spoilerTitle.classList.toggle("_spoiler-active");
                            _slideToggle(spoilerTitle.nextElementSibling, spoilerSpeed);
                            if (scrollspoiler && spoilerTitle.classList.contains("_spoiler-active")) {
                                const scrollspoilerValue = spoilerBlock.dataset.spoilerScroll;
                                const scrollspoilerOffset = +scrollspoilerValue ? +scrollspoilerValue : 0;
                                const scrollspoilerNoHeader = spoilerBlock.hasAttribute("data-spoiler-scroll-noheader") ? document.querySelector(".header").offsetHeight : 0;
                                window.scrollTo({
                                    top: spoilerBlock.offsetTop - (scrollspoilerOffset + scrollspoilerNoHeader),
                                    behavior: "smooth"
                                });
                            }
                        }
                    }
                }
                if (!el.closest("[data-spoilers]")) {
                    const spoilersClose = document.querySelectorAll("[data-spoiler-close]");
                    if (spoilersClose.length) spoilersClose.forEach((spoilerClose => {
                        const spoilersBlock = spoilerClose.closest("[data-spoilers]");
                        const spoilerCloseBlock = spoilerClose.parentNode;
                        if (spoilersBlock.classList.contains("_spoiler-init")) {
                            const spoilerSpeed = spoilersBlock.dataset.spoilersSpeed ? parseInt(spoilersBlock.dataset.spoilersSpeed) : 500;
                            spoilerClose.classList.remove("_spoiler-active");
                            _slideUp(spoilerClose.nextElementSibling, spoilerSpeed);
                            setTimeout((() => {
                                spoilerCloseBlock.open = false;
                            }), spoilerSpeed);
                        }
                    }));
                }
            }
            function hidespoilersBody(spoilersBlock) {
                const spoilerActiveBlock = spoilersBlock.querySelector("details[open]");
                if (spoilerActiveBlock && !spoilersBlock.querySelectorAll("._slide").length) {
                    const spoilerActiveTitle = spoilerActiveBlock.querySelector("summary");
                    const spoilerSpeed = spoilersBlock.dataset.spoilersSpeed ? parseInt(spoilersBlock.dataset.spoilersSpeed) : 500;
                    spoilerActiveTitle.classList.remove("_spoiler-active");
                    _slideUp(spoilerActiveTitle.nextElementSibling, spoilerSpeed);
                    setTimeout((() => {
                        spoilerActiveBlock.open = false;
                    }), spoilerSpeed);
                }
            }
        }
    }
    function tabs() {
        const tabs = document.querySelectorAll("[data-tabs]");
        let tabsActiveHash = [];
        if (tabs.length > 0) {
            const hash = functions_getHash();
            if (hash && hash.startsWith("tab-")) tabsActiveHash = hash.replace("tab-", "").split("-");
            tabs.forEach(((tabsBlock, index) => {
                tabsBlock.classList.add("_tab-init");
                tabsBlock.setAttribute("data-tabs-index", index);
                tabsBlock.addEventListener("click", setTabsAction);
                initTabs(tabsBlock);
                const splideElement = tabsBlock.querySelector("._splide-tabs");
                if (splideElement) {
                    const splide = new Splide(splideElement, {
                        speed: 300,
                        pagination: false,
                        updateOnMove: true,
                        flickMaxPages: 1,
                        flickPower: 100
                    }).mount();
                    splideElement.splideInstance = splide;
                    splide.on("mounted move", (newIndex => {
                        setActiveTab(tabsBlock, newIndex);
                        updateSelect(tabsBlock, newIndex);
                    }));
                    const tabsTitles = tabsBlock.querySelectorAll("[data-tabs-title]");
                    tabsTitles.forEach(((title, tabIndex) => {
                        title.addEventListener("click", (() => {
                            splide.go(tabIndex);
                        }));
                    }));
                    const activeIndex = Array.from(tabsTitles).findIndex((title => title.classList.contains("_tab-active")));
                    if (activeIndex >= 0) splide.go(activeIndex);
                }
                const tabsSelect = tabsBlock.querySelector(".select_tabs");
                if (tabsSelect) {
                    document.addEventListener("selectCallback", (e => {
                        if (e.detail.select === tabsSelect.querySelector("select")) {
                            const selectedOption = e.detail.select.selectedOptions[0];
                            const value = selectedOption.value;
                            if (!selectedOption.hasAttribute("data-href")) {
                                const tabIndex = parseInt(value, 10) - 1;
                                if (splideElement && splideElement.splideInstance) splideElement.splideInstance.go(tabIndex); else setActiveTab(tabsBlock, tabIndex);
                            }
                        }
                    }));
                    const activeTabIndex = Array.from(tabsBlock.querySelectorAll("[data-tabs-title]")).findIndex((title => title.classList.contains("_tab-active")));
                    if (activeTabIndex >= 0) tabsSelect.value = activeTabIndex + 1;
                }
            }));
            let mdQueriesArray = dataMediaQueries(tabs, "tabs");
            if (mdQueriesArray && mdQueriesArray.length) mdQueriesArray.forEach((mdQueriesItem => {
                mdQueriesItem.matchMedia.addEventListener("change", (function() {
                    setTitlePosition(mdQueriesItem.itemsArray, mdQueriesItem.matchMedia);
                }));
                setTitlePosition(mdQueriesItem.itemsArray, mdQueriesItem.matchMedia);
            }));
        }
        function setTitlePosition(tabsMediaArray, matchMedia) {
            tabsMediaArray.forEach((tabsMediaItem => {
                tabsMediaItem = tabsMediaItem.item;
                let tabsTitles = tabsMediaItem.querySelector("[data-tabs-titles]");
                let tabsTitleItems = tabsMediaItem.querySelectorAll("[data-tabs-title]");
                let tabsContent = tabsMediaItem.querySelector("[data-tabs-body]");
                let tabsContentItems = tabsMediaItem.querySelectorAll("[data-tabs-item]");
                tabsTitleItems = Array.from(tabsTitleItems).filter((item => item.closest("[data-tabs]") === tabsMediaItem));
                tabsContentItems = Array.from(tabsContentItems).filter((item => item.closest("[data-tabs]") === tabsMediaItem));
                tabsContentItems.forEach(((tabsContentItem, index) => {
                    if (matchMedia.matches) {
                        tabsContent.append(tabsTitleItems[index]);
                        tabsContent.append(tabsContentItem);
                        tabsMediaItem.classList.add("_tab-spoiler");
                    } else {
                        tabsTitles.append(tabsTitleItems[index]);
                        tabsMediaItem.classList.remove("_tab-spoiler");
                    }
                }));
            }));
        }
        function initTabs(tabsBlock) {
            let tabsTitles = tabsBlock.querySelectorAll("[data-tabs-titles] button");
            let tabsContent = tabsBlock.querySelectorAll("[data-tabs-body]>*");
            const tabsBlockIndex = tabsBlock.dataset.tabsIndex;
            const tabsActiveHashBlock = tabsActiveHash[0] == tabsBlockIndex;
            if (tabsActiveHashBlock) {
                const tabsActiveTitle = tabsBlock.querySelector("[data-tabs-titles]>._tab-active");
                tabsActiveTitle ? tabsActiveTitle.classList.remove("_tab-active") : null;
            }
            if (tabsContent.length) tabsContent.forEach(((tabsContentItem, index) => {
                tabsTitles[index].setAttribute("data-tabs-title", "");
                tabsContentItem.setAttribute("data-tabs-item", "");
                if (tabsActiveHashBlock && index == tabsActiveHash[1]) tabsTitles[index].classList.add("_tab-active");
                tabsContentItem.hidden = !tabsTitles[index].classList.contains("_tab-active");
                tabsContentItem.inert = !tabsTitles[index].classList.contains("_tab-active");
            }));
            const splideElement = tabsBlock.querySelector("._splide-tabs");
            if (splideElement && splideElement.splideInstance) {
                const activeIndex = Array.from(tabsTitles).findIndex((title => title.classList.contains("_tab-active")));
                if (activeIndex >= 0) splideElement.splideInstance.go(activeIndex);
            }
        }
        function setTabsStatus(tabsBlock) {
            let tabsTitles = tabsBlock.querySelectorAll("[data-tabs-title]");
            let tabsContent = tabsBlock.querySelectorAll("[data-tabs-item]");
            const tabsBlockIndex = tabsBlock.dataset.tabsIndex;
            function isTabsAnimate(tabsBlock) {
                if (tabsBlock.hasAttribute("data-tabs-animate")) return tabsBlock.dataset.tabsAnimate > 0 ? Number(tabsBlock.dataset.tabsAnimate) : 500;
            }
            const tabsBlockAnimate = isTabsAnimate(tabsBlock);
            if (tabsContent.length > 0) {
                const isHash = tabsBlock.hasAttribute("data-tabs-hash");
                tabsContent = Array.from(tabsContent).filter((item => item.closest("[data-tabs]") === tabsBlock));
                tabsTitles = Array.from(tabsTitles).filter((item => item.closest("[data-tabs]") === tabsBlock));
                tabsContent.forEach(((tabsContentItem, index) => {
                    if (tabsTitles[index].classList.contains("_tab-active")) {
                        if (tabsBlockAnimate) _slideDown(tabsContentItem, tabsBlockAnimate); else {
                            tabsContentItem.hidden = false;
                            tabsContentItem.inert = false;
                        }
                        if (isHash && !tabsContentItem.closest(".popup")) setHash(`tab-${tabsBlockIndex}-${index}`);
                    } else if (tabsBlockAnimate) _slideUp(tabsContentItem, tabsBlockAnimate); else {
                        tabsContentItem.hidden = true;
                        tabsContentItem.inert = true;
                    }
                }));
            }
        }
        function setTabsAction(e) {
            const el = e.target;
            if (el.closest("[data-tabs-title]")) {
                const tabTitle = el.closest("[data-tabs-title]");
                const tabsBlock = tabTitle.closest("[data-tabs]");
                const splideElement = tabsBlock.querySelector("._splide-tabs");
                if (splideElement && splideElement.splideInstance) {
                    const splide = splideElement.splideInstance;
                    const tabIndex = Array.from(tabTitle.parentElement.children).indexOf(tabTitle);
                    splide.go(tabIndex);
                } else if (!tabTitle.classList.contains("_tab-active") && !tabsBlock.querySelector("._slide")) {
                    let tabActiveTitle = tabsBlock.querySelectorAll("[data-tabs-title]._tab-active");
                    tabActiveTitle.length ? tabActiveTitle = Array.from(tabActiveTitle).filter((item => item.closest("[data-tabs]") === tabsBlock)) : null;
                    tabActiveTitle.length ? tabActiveTitle[0].classList.remove("_tab-active") : null;
                    tabTitle.classList.add("_tab-active");
                    setTabsStatus(tabsBlock);
                    const activeTabIndex = Array.from(tabsBlock.querySelectorAll("[data-tabs-title]")).indexOf(tabTitle);
                    updateSelect(tabsBlock, activeTabIndex);
                }
                const tabSwitchEvent = new CustomEvent("tabSwitch");
                tabsBlock.dispatchEvent(tabSwitchEvent);
                e.preventDefault();
            }
        }
        function setActiveTab(tabsBlock, index) {
            const tabsTitles = tabsBlock.querySelectorAll("[data-tabs-title]");
            const tabsContent = tabsBlock.querySelectorAll("[data-tabs-item]");
            tabsTitles.forEach(((title, i) => {
                title.classList.toggle("_tab-active", i === index);
                tabsContent[i].hidden = i !== index;
                tabsContent[i].inert = i !== index;
            }));
            updateSelect(tabsBlock, index);
        }
        function updateSelect(tabsBlock, index) {
            const tabsSelect = tabsBlock.querySelector(".tabs-select");
            if (tabsSelect) {
                tabsSelect.value = index + 1;
                modules_flsModules.select.selectBuild(tabsSelect);
            }
        }
    }
    function showMore() {
        window.addEventListener("load", (function(e) {
            const showMoreBlocks = document.querySelectorAll("[data-showmore]");
            let showMoreBlocksRegular;
            let mdQueriesArray;
            if (showMoreBlocks.length) {
                showMoreBlocksRegular = Array.from(showMoreBlocks).filter((function(item, index, self) {
                    return !item.dataset.showmoreMedia;
                }));
                showMoreBlocksRegular.length ? initItems(showMoreBlocksRegular) : null;
                document.addEventListener("click", showMoreActions);
                window.addEventListener("resize", showMoreActions);
                mdQueriesArray = dataMediaQueries(showMoreBlocks, "showmoreMedia");
                if (mdQueriesArray && mdQueriesArray.length) {
                    mdQueriesArray.forEach((mdQueriesItem => {
                        mdQueriesItem.matchMedia.addEventListener("change", (function() {
                            initItems(mdQueriesItem.itemsArray, mdQueriesItem.matchMedia);
                        }));
                    }));
                    initItemsMedia(mdQueriesArray);
                }
            }
            function initItemsMedia(mdQueriesArray) {
                mdQueriesArray.forEach((mdQueriesItem => {
                    initItems(mdQueriesItem.itemsArray, mdQueriesItem.matchMedia);
                }));
            }
            function initItems(showMoreBlocks, matchMedia) {
                showMoreBlocks.forEach((showMoreBlock => {
                    initItem(showMoreBlock, matchMedia);
                }));
            }
            function initItem(showMoreBlock, matchMedia = false) {
                showMoreBlock = matchMedia ? showMoreBlock.item : showMoreBlock;
                const tabsParent = showMoreBlock.closest("[data-tabs]");
                if (tabsParent && !tabsParent.hasListener) {
                    tabsParent.addEventListener("tabSwitch", (function() {
                        var showMoreContent = showMoreBlock.querySelector("[data-showmore-content]");
                        if (showMoreContent && showMoreContent.hidden) showMoreContent.hidden = false;
                        showMoreActions({
                            type: "resize"
                        });
                    }));
                    tabsParent.hasListener = true;
                }
                let showMoreContent = showMoreBlock.querySelectorAll("[data-showmore-content]");
                let showMoreButton = showMoreBlock.querySelectorAll("[data-showmore-button]");
                showMoreContent = Array.from(showMoreContent).filter((item => item.closest("[data-showmore]") === showMoreBlock))[0];
                showMoreButton = Array.from(showMoreButton).filter((item => item.closest("[data-showmore]") === showMoreBlock))[0];
                const hiddenHeight = getHeight(showMoreBlock, showMoreContent);
                if (matchMedia.matches || !matchMedia) if (hiddenHeight < getOriginalHeight(showMoreContent)) {
                    _slideUp(showMoreContent, 0, showMoreBlock.classList.contains("_showmore-active") ? getOriginalHeight(showMoreContent) : hiddenHeight);
                    showMoreButton.hidden = false;
                } else {
                    _slideDown(showMoreContent, 0, hiddenHeight);
                    showMoreButton.hidden = true;
                } else {
                    _slideDown(showMoreContent, 0, hiddenHeight);
                    showMoreButton.hidden = true;
                }
            }
            function getHeight(showMoreBlock, showMoreContent) {
                let hiddenHeight = 0;
                const showMoreType = showMoreBlock.dataset.showmore ? showMoreBlock.dataset.showmore : "size";
                const rowGap = parseFloat(getComputedStyle(showMoreContent).rowGap) ? parseFloat(getComputedStyle(showMoreContent).rowGap) : 0;
                if (showMoreType === "items") {
                    const showMoreTypeValue = showMoreContent.dataset.showmoreContent ? showMoreContent.dataset.showmoreContent : 3;
                    const showMoreItems = showMoreContent.children;
                    for (let index = 1; index < showMoreItems.length; index++) {
                        const showMoreItem = showMoreItems[index - 1];
                        const marginTop = parseFloat(getComputedStyle(showMoreItem).marginTop) ? parseFloat(getComputedStyle(showMoreItem).marginTop) : 0;
                        const marginBottom = parseFloat(getComputedStyle(showMoreItem).marginBottom) ? parseFloat(getComputedStyle(showMoreItem).marginBottom) : 0;
                        hiddenHeight += showMoreItem.offsetHeight + marginTop;
                        if (index == showMoreTypeValue) break;
                        hiddenHeight += marginBottom;
                    }
                    rowGap ? hiddenHeight += (showMoreTypeValue - 1) * rowGap : null;
                } else {
                    const showMoreTypeValue = showMoreContent.dataset.showmoreContent ? showMoreContent.dataset.showmoreContent : 150;
                    hiddenHeight = showMoreTypeValue;
                }
                return hiddenHeight;
            }
            function getOriginalHeight(showMoreContent) {
                let parentHidden;
                let hiddenHeight = showMoreContent.offsetHeight;
                showMoreContent.style.removeProperty("height");
                if (showMoreContent.closest(`[hidden]`)) {
                    parentHidden = showMoreContent.closest(`[hidden]`);
                    parentHidden.hidden = false;
                }
                let originalHeight = showMoreContent.offsetHeight;
                parentHidden ? parentHidden.hidden = true : null;
                showMoreContent.style.height = `${hiddenHeight}px`;
                return originalHeight;
            }
            function showMoreActions(e) {
                const targetEvent = e.target;
                const targetType = e.type;
                if (targetType === "click") {
                    if (targetEvent.closest("[data-showmore-button]")) {
                        const showMoreButton = targetEvent.closest("[data-showmore-button]");
                        const showMoreBlock = showMoreButton.closest("[data-showmore]");
                        const showMoreContent = showMoreBlock.querySelector("[data-showmore-content]");
                        const showMoreSpeed = showMoreBlock.dataset.showmoreButton ? showMoreBlock.dataset.showmoreButton : "500";
                        const hiddenHeight = getHeight(showMoreBlock, showMoreContent);
                        if (!showMoreContent.classList.contains("_slide")) {
                            showMoreBlock.classList.contains("_showmore-active") ? _slideUp(showMoreContent, showMoreSpeed, hiddenHeight) : _slideDown(showMoreContent, showMoreSpeed, hiddenHeight);
                            showMoreBlock.classList.toggle("_showmore-active");
                        }
                    }
                } else if (targetType === "resize") {
                    showMoreBlocksRegular && showMoreBlocksRegular.length ? initItems(showMoreBlocksRegular) : null;
                    mdQueriesArray && mdQueriesArray.length ? initItemsMedia(mdQueriesArray) : null;
                }
            }
        }));
    }
    function menuInit() {
        if (document.querySelector("[data-menu]")) document.addEventListener("click", (function(e) {
            if (bodyLockStatus && e.target.closest("[data-menu]")) {
                bodyLockToggle();
                document.documentElement.classList.toggle("menu-open");
            }
        }));
    }
    function functions_FLS(message) {
        setTimeout((() => {
            if (window.FLS) console.log(message);
        }), 0);
    }
    function uniqArray(array) {
        return array.filter((function(item, index, self) {
            return self.indexOf(item) === index;
        }));
    }
    function dataMediaQueries(array, dataSetValue) {
        const media = Array.from(array).filter((function(item, index, self) {
            if (item.dataset[dataSetValue]) return item.dataset[dataSetValue].split(",")[0];
        }));
        if (media.length) {
            const breakpointsArray = [];
            media.forEach((item => {
                const params = item.dataset[dataSetValue];
                const breakpoint = {};
                const paramsArray = params.split(",");
                breakpoint.value = paramsArray[0];
                breakpoint.type = paramsArray[1] ? paramsArray[1].trim() : "max";
                breakpoint.item = item;
                breakpointsArray.push(breakpoint);
            }));
            let mdQueries = breakpointsArray.map((function(item) {
                return "(" + item.type + "-width: " + item.value + "px)," + item.value + "," + item.type;
            }));
            mdQueries = uniqArray(mdQueries);
            const mdQueriesArray = [];
            if (mdQueries.length) {
                mdQueries.forEach((breakpoint => {
                    const paramsArray = breakpoint.split(",");
                    const mediaBreakpoint = paramsArray[1];
                    const mediaType = paramsArray[2];
                    const matchMedia = window.matchMedia(paramsArray[0]);
                    const itemsArray = breakpointsArray.filter((function(item) {
                        if (item.value === mediaBreakpoint && item.type === mediaType) return true;
                    }));
                    mdQueriesArray.push({
                        itemsArray,
                        matchMedia
                    });
                }));
                return mdQueriesArray;
            }
        }
    }
    !function(e) {
        "function" == typeof define && define.amd ? define([], e) : "object" == typeof exports ? module.exports = e() : window.wNumb = e();
    }((function() {
        "use strict";
        var o = [ "decimals", "thousand", "mark", "prefix", "suffix", "encoder", "decoder", "negativeBefore", "negative", "edit", "undo" ];
        function w(e) {
            return e.split("").reverse().join("");
        }
        function h(e, t) {
            return e.substring(0, t.length) === t;
        }
        function f(e, t, n) {
            if ((e[t] || e[n]) && e[t] === e[n]) throw new Error(t);
        }
        function x(e) {
            return "number" == typeof e && isFinite(e);
        }
        function n(e, t, n, r, i, o, f, u, s, c, a, p) {
            var d, l, h, g = p, v = "", m = "";
            return o && (p = o(p)), !!x(p) && (!1 !== e && 0 === parseFloat(p.toFixed(e)) && (p = 0), 
            p < 0 && (d = !0, p = Math.abs(p)), !1 !== e && (p = function(e, t) {
                return e = e.toString().split("e"), (+((e = (e = Math.round(+(e[0] + "e" + (e[1] ? +e[1] + t : t)))).toString().split("e"))[0] + "e" + (e[1] ? e[1] - t : -t))).toFixed(t);
            }(p, e)), -1 !== (p = p.toString()).indexOf(".") ? (h = (l = p.split("."))[0], n && (v = n + l[1])) : h = p, 
            t && (h = w((h = w(h).match(/.{1,3}/g)).join(w(t)))), d && u && (m += u), r && (m += r), 
            d && s && (m += s), m += h, m += v, i && (m += i), c && (m = c(m, g)), m);
        }
        function r(e, t, n, r, i, o, f, u, s, c, a, p) {
            var d, l = "";
            return a && (p = a(p)), !(!p || "string" != typeof p) && (u && h(p, u) && (p = p.replace(u, ""), 
            d = !0), r && h(p, r) && (p = p.replace(r, "")), s && h(p, s) && (p = p.replace(s, ""), 
            d = !0), i && function(e, t) {
                return e.slice(-1 * t.length) === t;
            }(p, i) && (p = p.slice(0, -1 * i.length)), t && (p = p.split(t).join("")), n && (p = p.replace(n, ".")), 
            d && (l += "-"), "" !== (l = (l += p).replace(/[^0-9\.\-.]/g, "")) && (l = Number(l), 
            f && (l = f(l)), !!x(l) && l));
        }
        function i(e, t, n) {
            var r, i = [];
            for (r = 0; r < o.length; r += 1) i.push(e[o[r]]);
            return i.push(n), t.apply("", i);
        }
        return function e(t) {
            if (!(this instanceof e)) return new e(t);
            "object" == typeof t && (t = function(e) {
                var t, n, r, i = {};
                for (void 0 === e.suffix && (e.suffix = e.postfix), t = 0; t < o.length; t += 1) if (void 0 === (r = e[n = o[t]])) "negative" !== n || i.negativeBefore ? "mark" === n && "." !== i.thousand ? i[n] = "." : i[n] = !1 : i[n] = "-"; else if ("decimals" === n) {
                    if (!(0 <= r && r < 8)) throw new Error(n);
                    i[n] = r;
                } else if ("encoder" === n || "decoder" === n || "edit" === n || "undo" === n) {
                    if ("function" != typeof r) throw new Error(n);
                    i[n] = r;
                } else {
                    if ("string" != typeof r) throw new Error(n);
                    i[n] = r;
                }
                return f(i, "mark", "thousand"), f(i, "prefix", "negative"), f(i, "prefix", "negativeBefore"), 
                i;
            }(t), this.to = function(e) {
                return i(t, n, e);
            }, this.from = function(e) {
                return i(t, r, e);
            });
        };
    }));
    class Popup {
        constructor(options) {
            let config = {
                logging: true,
                init: true,
                attributeOpenButton: "data-popup",
                attributeCloseButton: "data-close",
                fixElementSelector: "[data-lp]",
                youtubeAttribute: "data-popup-youtube",
                youtubePlaceAttribute: "data-popup-youtube-place",
                setAutoplayYoutube: true,
                classes: {
                    popup: "popup",
                    popupContent: "popup__content",
                    popupActive: "popup_show",
                    bodyActive: "popup-show"
                },
                focusCatch: true,
                closeEsc: true,
                bodyLock: true,
                hashSettings: {
                    location: true,
                    goHash: true
                },
                on: {
                    beforeOpen: function() {},
                    afterOpen: function() {},
                    beforeClose: function() {},
                    afterClose: function() {}
                }
            };
            this.youTubeCode;
            this.isOpen = false;
            this.targetOpen = {
                selector: false,
                element: false
            };
            this.previousOpen = {
                selector: false,
                element: false
            };
            this.lastClosed = {
                selector: false,
                element: false
            };
            this._dataValue = false;
            this.hash = false;
            this._reopen = false;
            this._selectorOpen = false;
            this.lastFocusEl = false;
            this._focusEl = [ "a[href]", 'input:not([disabled]):not([type="hidden"]):not([aria-hidden])', "button:not([disabled]):not([aria-hidden])", "select:not([disabled]):not([aria-hidden])", "textarea:not([disabled]):not([aria-hidden])", "area[href]", "iframe", "object", "embed", "[contenteditable]", '[tabindex]:not([tabindex^="-"])' ];
            this.options = {
                ...config,
                ...options,
                classes: {
                    ...config.classes,
                    ...options?.classes
                },
                hashSettings: {
                    ...config.hashSettings,
                    ...options?.hashSettings
                },
                on: {
                    ...config.on,
                    ...options?.on
                }
            };
            this.bodyLock = false;
            this.options.init ? this.initPopups() : null;
        }
        initPopups() {
            this.popupLogging(`Прокинувся`);
            this.eventsPopup();
        }
        eventsPopup() {
            document.addEventListener("click", function(e) {
                const buttonOpen = e.target.closest(`[${this.options.attributeOpenButton}]`);
                if (buttonOpen) {
                    e.preventDefault();
                    this._dataValue = buttonOpen.getAttribute(this.options.attributeOpenButton) ? buttonOpen.getAttribute(this.options.attributeOpenButton) : "error";
                    this.youTubeCode = buttonOpen.getAttribute(this.options.youtubeAttribute) ? buttonOpen.getAttribute(this.options.youtubeAttribute) : null;
                    if (this._dataValue !== "error") {
                        if (!this.isOpen) this.lastFocusEl = buttonOpen;
                        this.targetOpen.selector = `${this._dataValue}`;
                        this._selectorOpen = true;
                        this.open();
                        return;
                    } else this.popupLogging(`Йой, не заповнено атрибут у ${buttonOpen.classList}`);
                    return;
                }
                const buttonClose = e.target.closest(`[${this.options.attributeCloseButton}]`);
                if (buttonClose || !e.target.closest(`.${this.options.classes.popupContent}`) && this.isOpen) {
                    e.preventDefault();
                    this.close();
                    return;
                }
            }.bind(this));
            document.addEventListener("keydown", function(e) {
                if (this.options.closeEsc && e.which == 27 && e.code === "Escape" && this.isOpen) {
                    e.preventDefault();
                    this.close();
                    return;
                }
                if (this.options.focusCatch && e.which == 9 && this.isOpen) {
                    this._focusCatch(e);
                    return;
                }
            }.bind(this));
            if (this.options.hashSettings.goHash) {
                window.addEventListener("hashchange", function() {
                    if (window.location.hash) this._openToHash(); else this.close(this.targetOpen.selector);
                }.bind(this));
                window.addEventListener("load", function() {
                    if (window.location.hash) this._openToHash();
                }.bind(this));
            }
        }
        open(selectorValue) {
            if (bodyLockStatus) {
                this.bodyLock = document.documentElement.classList.contains("lock") && !this.isOpen ? true : false;
                if (selectorValue && typeof selectorValue === "string" && selectorValue.trim() !== "") {
                    this.targetOpen.selector = selectorValue;
                    this._selectorOpen = true;
                }
                if (this.isOpen) {
                    this._reopen = true;
                    this.close();
                }
                if (!this._selectorOpen) this.targetOpen.selector = this.lastClosed.selector;
                if (!this._reopen) this.previousActiveElement = document.activeElement;
                this.targetOpen.element = document.querySelector(this.targetOpen.selector);
                if (this.targetOpen.element) {
                    if (this.youTubeCode) {
                        const codeVideo = this.youTubeCode;
                        const urlVideo = `https://www.youtube.com/embed/${codeVideo}?rel=0&showinfo=0&autoplay=1`;
                        const iframe = document.createElement("iframe");
                        iframe.setAttribute("allowfullscreen", "");
                        const autoplay = this.options.setAutoplayYoutube ? "autoplay;" : "";
                        iframe.setAttribute("allow", `${autoplay}; encrypted-media`);
                        iframe.setAttribute("src", urlVideo);
                        if (!this.targetOpen.element.querySelector(`[${this.options.youtubePlaceAttribute}]`)) {
                            this.targetOpen.element.querySelector(".popup__text").setAttribute(`${this.options.youtubePlaceAttribute}`, "");
                        }
                        this.targetOpen.element.querySelector(`[${this.options.youtubePlaceAttribute}]`).appendChild(iframe);
                    }
                    if (this.options.hashSettings.location) {
                        this._getHash();
                        this._setHash();
                    }
                    this.options.on.beforeOpen(this);
                    document.dispatchEvent(new CustomEvent("beforePopupOpen", {
                        detail: {
                            popup: this
                        }
                    }));
                    this.targetOpen.element.classList.add(this.options.classes.popupActive);
                    document.documentElement.classList.add(this.options.classes.bodyActive);
                    if (!this._reopen) !this.bodyLock ? bodyLock() : null; else this._reopen = false;
                    this.targetOpen.element.setAttribute("aria-hidden", "false");
                    this.previousOpen.selector = this.targetOpen.selector;
                    this.previousOpen.element = this.targetOpen.element;
                    this._selectorOpen = false;
                    this.isOpen = true;
                    setTimeout((() => {
                        this._focusTrap();
                    }), 50);
                    this.options.on.afterOpen(this);
                    document.dispatchEvent(new CustomEvent("afterPopupOpen", {
                        detail: {
                            popup: this
                        }
                    }));
                    this.popupLogging(`Відкрив попап`);
                } else this.popupLogging(`Йой, такого попапу немає. Перевірте коректність введення. `);
            }
        }
        close(selectorValue) {
            if (selectorValue && typeof selectorValue === "string" && selectorValue.trim() !== "") this.previousOpen.selector = selectorValue;
            if (!this.isOpen || !bodyLockStatus) return;
            this.options.on.beforeClose(this);
            document.dispatchEvent(new CustomEvent("beforePopupClose", {
                detail: {
                    popup: this
                }
            }));
            if (this.youTubeCode) if (this.targetOpen.element.querySelector(`[${this.options.youtubePlaceAttribute}]`)) this.targetOpen.element.querySelector(`[${this.options.youtubePlaceAttribute}]`).innerHTML = "";
            this.previousOpen.element.classList.remove(this.options.classes.popupActive);
            this.previousOpen.element.setAttribute("aria-hidden", "true");
            if (!this._reopen) {
                document.documentElement.classList.remove(this.options.classes.bodyActive);
                !this.bodyLock ? bodyUnlock() : null;
                this.isOpen = false;
            }
            this._removeHash();
            if (this._selectorOpen) {
                this.lastClosed.selector = this.previousOpen.selector;
                this.lastClosed.element = this.previousOpen.element;
            }
            this.options.on.afterClose(this);
            document.dispatchEvent(new CustomEvent("afterPopupClose", {
                detail: {
                    popup: this
                }
            }));
            setTimeout((() => {
                this._focusTrap();
            }), 50);
            this.popupLogging(`Закрив попап`);
        }
        _getHash() {
            if (this.options.hashSettings.location) this.hash = this.targetOpen.selector.includes("#") ? this.targetOpen.selector : this.targetOpen.selector.replace(".", "#");
        }
        _openToHash() {
            let classInHash = document.querySelector(`.${window.location.hash.replace("#", "")}`) ? `.${window.location.hash.replace("#", "")}` : document.querySelector(`${window.location.hash}`) ? `${window.location.hash}` : null;
            const buttons = document.querySelector(`[${this.options.attributeOpenButton} = "${classInHash}"]`) ? document.querySelector(`[${this.options.attributeOpenButton} = "${classInHash}"]`) : document.querySelector(`[${this.options.attributeOpenButton} = "${classInHash.replace(".", "#")}"]`);
            this.youTubeCode = buttons.getAttribute(this.options.youtubeAttribute) ? buttons.getAttribute(this.options.youtubeAttribute) : null;
            if (buttons && classInHash) this.open(classInHash);
        }
        _setHash() {
            history.pushState("", "", this.hash);
        }
        _removeHash() {
            history.pushState("", "", window.location.href.split("#")[0]);
        }
        _focusCatch(e) {
            const focusable = this.targetOpen.element.querySelectorAll(this._focusEl);
            const focusArray = Array.prototype.slice.call(focusable);
            const focusedIndex = focusArray.indexOf(document.activeElement);
            if (e.shiftKey && focusedIndex === 0) {
                focusArray[focusArray.length - 1].focus();
                e.preventDefault();
            }
            if (!e.shiftKey && focusedIndex === focusArray.length - 1) {
                focusArray[0].focus();
                e.preventDefault();
            }
        }
        _focusTrap() {
            const focusable = this.previousOpen.element.querySelectorAll(this._focusEl);
            if (!this.isOpen && this.lastFocusEl) this.lastFocusEl.focus(); else focusable[0].focus();
        }
        popupLogging(message) {
            this.options.logging ? functions_FLS(`[Попапос]: ${message}`) : null;
        }
    }
    modules_flsModules.popup = new Popup({});
    let formValidate = {
        getErrors(form) {
            let error = 0;
            let formRequiredItems = form.querySelectorAll("*[data-required]");
            if (formRequiredItems.length) formRequiredItems.forEach((formRequiredItem => {
                if ((formRequiredItem.offsetParent !== null || formRequiredItem.tagName === "SELECT") && !formRequiredItem.disabled) error += this.validateInput(formRequiredItem);
            }));
            return error;
        },
        validateInput(formRequiredItem) {
            let error = 0;
            if (formRequiredItem.dataset.required === "email") {
                formRequiredItem.value = formRequiredItem.value.replace(" ", "");
                if (this.emailTest(formRequiredItem)) {
                    this.addError(formRequiredItem);
                    error++;
                } else this.removeError(formRequiredItem);
            } else if (formRequiredItem.type === "checkbox" && !formRequiredItem.checked) {
                this.addError(formRequiredItem);
                error++;
            } else if (!formRequiredItem.value.trim()) {
                this.addError(formRequiredItem);
                error++;
            } else this.removeError(formRequiredItem);
            return error;
        },
        addError(formRequiredItem) {
            formRequiredItem.classList.add("_form-error");
            formRequiredItem.parentElement.classList.add("_form-error");
            let inputError = formRequiredItem.parentElement.querySelector(".form__error");
            if (inputError) formRequiredItem.parentElement.removeChild(inputError);
            if (formRequiredItem.dataset.error) formRequiredItem.parentElement.insertAdjacentHTML("beforeend", `<div class="form__error">${formRequiredItem.dataset.error}</div>`);
        },
        removeError(formRequiredItem) {
            formRequiredItem.classList.remove("_form-error");
            formRequiredItem.parentElement.classList.remove("_form-error");
            if (formRequiredItem.parentElement.querySelector(".form__error")) formRequiredItem.parentElement.removeChild(formRequiredItem.parentElement.querySelector(".form__error"));
        },
        formClean(form) {
            form.reset();
            setTimeout((() => {
                let inputs = form.querySelectorAll("input,textarea");
                for (let index = 0; index < inputs.length; index++) {
                    const el = inputs[index];
                    el.parentElement.classList.remove("_form-focus");
                    el.classList.remove("_form-focus");
                    formValidate.removeError(el);
                }
                let checkboxes = form.querySelectorAll(".checkbox__input");
                if (checkboxes.length > 0) for (let index = 0; index < checkboxes.length; index++) {
                    const checkbox = checkboxes[index];
                    checkbox.checked = false;
                }
                if (modules_flsModules.select) {
                    let selects = form.querySelectorAll("div.select");
                    if (selects.length) for (let index = 0; index < selects.length; index++) {
                        const select = selects[index].querySelector("select");
                        modules_flsModules.select.selectBuild(select);
                    }
                }
            }), 0);
        },
        emailTest(formRequiredItem) {
            return !/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,8})+$/.test(formRequiredItem.value);
        }
    };
    class SelectConstructor {
        constructor(props, data = null) {
            let defaultConfig = {
                init: true,
                logging: true,
                speed: 150
            };
            this.config = Object.assign(defaultConfig, props);
            this.selectClasses = {
                classSelect: "select",
                classSelectBody: "select__body",
                classSelectTitle: "select__title",
                classSelectValue: "select__value",
                classSelectLabel: "select__label",
                classSelectInput: "select__input",
                classSelectText: "select__text",
                classSelectLink: "select__link",
                classSelectOptions: "select__options",
                classSelectOptionsScroll: "select__scroll",
                classSelectOption: "select__option",
                classSelectContent: "select__content",
                classSelectRow: "select__row",
                classSelectData: "select__asset",
                classSelectDisabled: "_select-disabled",
                classSelectTag: "_select-tag",
                classSelectOpen: "_select-open",
                classSelectActive: "_select-active",
                classSelectFocus: "_select-focus",
                classSelectMultiple: "_select-multiple",
                classSelectCheckBox: "_select-checkbox",
                classSelectOptionSelected: "_select-selected",
                classSelectPseudoLabel: "_select-pseudo-label"
            };
            this._this = this;
            if (this.config.init) {
                const selectItems = data ? document.querySelectorAll(data) : document.querySelectorAll("select");
                if (selectItems.length) {
                    this.selectsInit(selectItems);
                    this.setLogging(`Прокинувся, построїв селектов: (${selectItems.length})`);
                } else this.setLogging("Сплю, немає жодного select");
            }
        }
        getSelectClass(className) {
            return `.${className}`;
        }
        getSelectElement(selectItem, className) {
            return {
                originalSelect: selectItem.querySelector("select"),
                selectElement: selectItem.querySelector(this.getSelectClass(className))
            };
        }
        selectsInit(selectItems) {
            selectItems.forEach(((originalSelect, index) => {
                this.selectInit(originalSelect, index + 1);
            }));
            document.addEventListener("click", function(e) {
                this.selectsActions(e);
            }.bind(this));
            document.addEventListener("keydown", function(e) {
                this.selectsActions(e);
            }.bind(this));
            document.addEventListener("focusin", function(e) {
                this.selectsActions(e);
            }.bind(this));
            document.addEventListener("focusout", function(e) {
                this.selectsActions(e);
            }.bind(this));
        }
        selectInit(originalSelect, index) {
            const _this = this;
            let selectItem = document.createElement("div");
            selectItem.classList.add(this.selectClasses.classSelect);
            originalSelect.parentNode.insertBefore(selectItem, originalSelect);
            selectItem.appendChild(originalSelect);
            originalSelect.hidden = true;
            index ? originalSelect.dataset.id = index : null;
            if (this.getSelectPlaceholder(originalSelect)) {
                originalSelect.dataset.placeholder = this.getSelectPlaceholder(originalSelect).value;
                if (this.getSelectPlaceholder(originalSelect).label.show) {
                    const selectItemTitle = this.getSelectElement(selectItem, this.selectClasses.classSelectTitle).selectElement;
                    selectItemTitle.insertAdjacentHTML("afterbegin", `<span class="${this.selectClasses.classSelectLabel}">${this.getSelectPlaceholder(originalSelect).label.text ? this.getSelectPlaceholder(originalSelect).label.text : this.getSelectPlaceholder(originalSelect).value}</span>`);
                }
            }
            selectItem.insertAdjacentHTML("beforeend", `<div class="${this.selectClasses.classSelectBody}"><div hidden class="${this.selectClasses.classSelectOptions}"></div></div>`);
            this.selectBuild(originalSelect);
            originalSelect.dataset.speed = originalSelect.dataset.speed ? originalSelect.dataset.speed : this.config.speed;
            this.config.speed = +originalSelect.dataset.speed;
            originalSelect.addEventListener("change", (function(e) {
                _this.selectChange(e);
            }));
        }
        selectBuild(originalSelect) {
            const selectItem = originalSelect.parentElement;
            selectItem.dataset.id = originalSelect.dataset.id;
            originalSelect.dataset.classModif ? selectItem.classList.add(`select_${originalSelect.dataset.classModif}`) : null;
            originalSelect.multiple ? selectItem.classList.add(this.selectClasses.classSelectMultiple) : selectItem.classList.remove(this.selectClasses.classSelectMultiple);
            originalSelect.hasAttribute("data-checkbox") && originalSelect.multiple ? selectItem.classList.add(this.selectClasses.classSelectCheckBox) : selectItem.classList.remove(this.selectClasses.classSelectCheckBox);
            this.setSelectTitleValue(selectItem, originalSelect);
            this.setOptions(selectItem, originalSelect);
            originalSelect.hasAttribute("data-search") ? this.searchActions(selectItem) : null;
            originalSelect.hasAttribute("data-open") ? this.selectAction(selectItem) : null;
            this.selectDisabled(selectItem, originalSelect);
        }
        selectsActions(e) {
            const targetElement = e.target;
            const targetType = e.type;
            if (targetElement.closest(this.getSelectClass(this.selectClasses.classSelect)) || targetElement.closest(this.getSelectClass(this.selectClasses.classSelectTag))) {
                const selectItem = targetElement.closest(".select") ? targetElement.closest(".select") : document.querySelector(`.${this.selectClasses.classSelect}[data-id="${targetElement.closest(this.getSelectClass(this.selectClasses.classSelectTag)).dataset.selectId}"]`);
                const originalSelect = this.getSelectElement(selectItem).originalSelect;
                if (targetType === "click") {
                    if (!originalSelect.disabled) if (targetElement.closest(this.getSelectClass(this.selectClasses.classSelectTag))) {
                        const targetTag = targetElement.closest(this.getSelectClass(this.selectClasses.classSelectTag));
                        const optionItem = document.querySelector(`.${this.selectClasses.classSelect}[data-id="${targetTag.dataset.selectId}"] .select__option[data-value="${targetTag.dataset.value}"]`);
                        this.optionAction(selectItem, originalSelect, optionItem);
                    } else if (targetElement.closest(this.getSelectClass(this.selectClasses.classSelectTitle))) this.selectAction(selectItem); else if (targetElement.closest(this.getSelectClass(this.selectClasses.classSelectOption))) {
                        const optionItem = targetElement.closest(this.getSelectClass(this.selectClasses.classSelectOption));
                        this.optionAction(selectItem, originalSelect, optionItem);
                    }
                } else if (targetType === "focusin" || targetType === "focusout") {
                    if (targetElement.closest(this.getSelectClass(this.selectClasses.classSelect))) targetType === "focusin" ? selectItem.classList.add(this.selectClasses.classSelectFocus) : selectItem.classList.remove(this.selectClasses.classSelectFocus);
                } else if (targetType === "keydown" && e.code === "Escape") this.selectsСlose();
            } else this.selectsСlose();
        }
        selectsСlose(selectOneGroup) {
            const selectsGroup = selectOneGroup ? selectOneGroup : document;
            const selectActiveItems = selectsGroup.querySelectorAll(`${this.getSelectClass(this.selectClasses.classSelect)}${this.getSelectClass(this.selectClasses.classSelectOpen)}`);
            if (selectActiveItems.length) selectActiveItems.forEach((selectActiveItem => {
                this.selectСlose(selectActiveItem);
            }));
        }
        selectСlose(selectItem) {
            const originalSelect = this.getSelectElement(selectItem).originalSelect;
            const selectOptions = this.getSelectElement(selectItem, this.selectClasses.classSelectOptions).selectElement;
            if (!selectOptions.classList.contains("_slide")) {
                selectItem.classList.remove(this.selectClasses.classSelectOpen);
                _slideUp(selectOptions, originalSelect.dataset.speed);
                setTimeout((() => {
                    selectItem.style.zIndex = "";
                }), originalSelect.dataset.speed);
            }
        }
        selectAction(selectItem) {
            const originalSelect = this.getSelectElement(selectItem).originalSelect;
            const selectOptions = this.getSelectElement(selectItem, this.selectClasses.classSelectOptions).selectElement;
            const selectOpenzIndex = originalSelect.dataset.zIndex ? originalSelect.dataset.zIndex : 3;
            this.setOptionsPosition(selectItem);
            if (originalSelect.closest("[data-one-select]")) {
                const selectOneGroup = originalSelect.closest("[data-one-select]");
                this.selectsСlose(selectOneGroup);
            }
            setTimeout((() => {
                if (!selectOptions.classList.contains("_slide")) {
                    selectItem.classList.toggle(this.selectClasses.classSelectOpen);
                    _slideToggle(selectOptions, originalSelect.dataset.speed);
                    if (selectItem.classList.contains(this.selectClasses.classSelectOpen)) selectItem.style.zIndex = selectOpenzIndex; else setTimeout((() => {
                        selectItem.style.zIndex = "";
                    }), originalSelect.dataset.speed);
                }
            }), 0);
        }
        setSelectTitleValue(selectItem, originalSelect) {
            const selectItemBody = this.getSelectElement(selectItem, this.selectClasses.classSelectBody).selectElement;
            const selectItemTitle = this.getSelectElement(selectItem, this.selectClasses.classSelectTitle).selectElement;
            if (selectItemTitle) selectItemTitle.remove();
            selectItemBody.insertAdjacentHTML("afterbegin", this.getSelectTitleValue(selectItem, originalSelect));
            originalSelect.hasAttribute("data-search") ? this.searchActions(selectItem) : null;
        }
        getSelectTitleValue(selectItem, originalSelect) {
            let selectTitleValue = this.getSelectedOptionsData(originalSelect, 2).html;
            if (originalSelect.multiple && originalSelect.hasAttribute("data-tags")) {
                selectTitleValue = this.getSelectedOptionsData(originalSelect).elements.map((option => `<span role="button" data-select-id="${selectItem.dataset.id}" data-value="${option.value}" class="_select-tag">${this.getSelectElementContent(option)}</span>`)).join("");
                if (originalSelect.dataset.tags && document.querySelector(originalSelect.dataset.tags)) {
                    document.querySelector(originalSelect.dataset.tags).innerHTML = selectTitleValue;
                    if (originalSelect.hasAttribute("data-search")) selectTitleValue = false;
                }
            }
            selectTitleValue = selectTitleValue.length ? selectTitleValue : originalSelect.dataset.placeholder ? originalSelect.dataset.placeholder : "";
            let pseudoAttribute = "";
            let pseudoAttributeClass = "";
            if (originalSelect.hasAttribute("data-pseudo-label")) {
                pseudoAttribute = originalSelect.dataset.pseudoLabel ? ` data-pseudo-label="${originalSelect.dataset.pseudoLabel}"` : ` data-pseudo-label="Заповніть атрибут"`;
                pseudoAttributeClass = ` ${this.selectClasses.classSelectPseudoLabel}`;
            }
            this.getSelectedOptionsData(originalSelect).values.length ? selectItem.classList.add(this.selectClasses.classSelectActive) : selectItem.classList.remove(this.selectClasses.classSelectActive);
            if (originalSelect.hasAttribute("data-search")) return `<div class="${this.selectClasses.classSelectTitle}"><span${pseudoAttribute} class="${this.selectClasses.classSelectValue}"><input autocomplete="off" type="text" placeholder="${selectTitleValue}" data-placeholder="${selectTitleValue}" class="${this.selectClasses.classSelectInput}"></span></div>`; else {
                const customClass = this.getSelectedOptionsData(originalSelect).elements.length && this.getSelectedOptionsData(originalSelect).elements[0].dataset.class ? ` ${this.getSelectedOptionsData(originalSelect).elements[0].dataset.class}` : "";
                return `<button type="button" class="${this.selectClasses.classSelectTitle}"><span${pseudoAttribute} class="${this.selectClasses.classSelectValue}${pseudoAttributeClass}"><span class="${this.selectClasses.classSelectContent}${customClass}">${selectTitleValue}</span></span></button>`;
            }
        }
        getSelectElementContent(selectOption) {
            const selectOptionData = selectOption.dataset.asset ? `${selectOption.dataset.asset}` : "";
            const selectOptionDataHTML = selectOptionData.indexOf("img") >= 0 ? `<img src="${selectOptionData}" alt="">` : selectOptionData;
            let selectOptionContentHTML = ``;
            selectOptionContentHTML += selectOptionData ? `<span class="${this.selectClasses.classSelectRow}">` : "";
            selectOptionContentHTML += selectOptionData ? `<span class="${this.selectClasses.classSelectData}">` : "";
            selectOptionContentHTML += selectOptionData ? selectOptionDataHTML : "";
            selectOptionContentHTML += selectOptionData ? `</span>` : "";
            selectOptionContentHTML += selectOptionData ? `<span class="${this.selectClasses.classSelectText}">` : "";
            selectOptionContentHTML += selectOption.textContent;
            selectOptionContentHTML += selectOptionData ? `</span>` : "";
            selectOptionContentHTML += selectOptionData ? `</span>` : "";
            return selectOptionContentHTML;
        }
        getSelectPlaceholder(originalSelect) {
            const selectPlaceholder = Array.from(originalSelect.options).find((option => !option.value));
            if (selectPlaceholder) return {
                value: selectPlaceholder.textContent,
                show: selectPlaceholder.hasAttribute("data-show"),
                label: {
                    show: selectPlaceholder.hasAttribute("data-label"),
                    text: selectPlaceholder.dataset.label
                }
            };
        }
        getSelectedOptionsData(originalSelect, type) {
            let selectedOptions = [];
            if (originalSelect.multiple) selectedOptions = Array.from(originalSelect.options).filter((option => option.value)).filter((option => option.selected)); else selectedOptions.push(originalSelect.options[originalSelect.selectedIndex]);
            return {
                elements: selectedOptions.map((option => option)),
                values: selectedOptions.filter((option => option.value)).map((option => option.value)),
                html: selectedOptions.map((option => this.getSelectElementContent(option)))
            };
        }
        getOptions(originalSelect) {
            const selectOptionsScroll = originalSelect.hasAttribute("data-scroll") ? `data-simplebar` : "";
            const customMaxHeightValue = +originalSelect.dataset.scroll ? +originalSelect.dataset.scroll : null;
            let selectOptions = Array.from(originalSelect.options);
            if (selectOptions.length > 0) {
                let selectOptionsHTML = ``;
                if (this.getSelectPlaceholder(originalSelect) && !this.getSelectPlaceholder(originalSelect).show || originalSelect.multiple) selectOptions = selectOptions.filter((option => option.value));
                selectOptionsHTML += `<div ${selectOptionsScroll} ${selectOptionsScroll ? `style="max-height: ${customMaxHeightValue}px"` : ""} class="${this.selectClasses.classSelectOptionsScroll}">`;
                selectOptions.forEach((selectOption => {
                    selectOptionsHTML += this.getOption(selectOption, originalSelect);
                }));
                selectOptionsHTML += `</div>`;
                return selectOptionsHTML;
            }
        }
        getOption(selectOption, originalSelect) {
            const selectOptionSelected = selectOption.selected && originalSelect.multiple ? ` ${this.selectClasses.classSelectOptionSelected}` : "";
            const selectOptionHide = selectOption.selected && !originalSelect.hasAttribute("data-show-selected") && !originalSelect.multiple ? `hidden` : ``;
            const selectOptionClass = selectOption.dataset.class ? ` ${selectOption.dataset.class}` : "";
            const selectOptionLink = selectOption.dataset.href ? selectOption.dataset.href : false;
            const selectOptionLinkTarget = selectOption.hasAttribute("data-href-blank") ? `target="_blank"` : "";
            let selectOptionHTML = ``;
            selectOptionHTML += selectOptionLink ? `<a ${selectOptionLinkTarget} ${selectOptionHide} href="${selectOptionLink}" data-value="${selectOption.value}" class="${this.selectClasses.classSelectOption}${selectOptionClass}${selectOptionSelected}">` : `<button ${selectOptionHide} class="${this.selectClasses.classSelectOption}${selectOptionClass}${selectOptionSelected}" data-value="${selectOption.value}" type="button">`;
            selectOptionHTML += this.getSelectElementContent(selectOption);
            selectOptionHTML += selectOptionLink ? `</a>` : `</button>`;
            return selectOptionHTML;
        }
        setOptions(selectItem, originalSelect) {
            const selectItemOptions = this.getSelectElement(selectItem, this.selectClasses.classSelectOptions).selectElement;
            selectItemOptions.innerHTML = this.getOptions(originalSelect);
        }
        setOptionsPosition(selectItem) {
            const originalSelect = this.getSelectElement(selectItem).originalSelect;
            const selectOptions = this.getSelectElement(selectItem, this.selectClasses.classSelectOptions).selectElement;
            const selectItemScroll = this.getSelectElement(selectItem, this.selectClasses.classSelectOptionsScroll).selectElement;
            const customMaxHeightValue = +originalSelect.dataset.scroll ? `${+originalSelect.dataset.scroll}px` : ``;
            const selectOptionsPosMargin = +originalSelect.dataset.optionsMargin ? +originalSelect.dataset.optionsMargin : 10;
            if (!selectItem.classList.contains(this.selectClasses.classSelectOpen)) {
                selectOptions.hidden = false;
                const selectItemScrollHeight = selectItemScroll.offsetHeight ? selectItemScroll.offsetHeight : parseInt(window.getComputedStyle(selectItemScroll).getPropertyValue("max-height"));
                const selectOptionsHeight = selectOptions.offsetHeight > selectItemScrollHeight ? selectOptions.offsetHeight : selectItemScrollHeight + selectOptions.offsetHeight;
                const selectOptionsScrollHeight = selectOptionsHeight - selectItemScrollHeight;
                selectOptions.hidden = true;
                const selectItemHeight = selectItem.offsetHeight;
                const selectItemPos = selectItem.getBoundingClientRect().top;
                const selectItemTotal = selectItemPos + selectOptionsHeight + selectItemHeight + selectOptionsScrollHeight;
                const selectItemResult = window.innerHeight - (selectItemTotal + selectOptionsPosMargin);
                if (selectItemResult < 0) {
                    const newMaxHeightValue = selectOptionsHeight + selectItemResult;
                    if (newMaxHeightValue < 100) {
                        selectItem.classList.add("select--show-top");
                        selectItemScroll.style.maxHeight = selectItemPos < selectOptionsHeight ? `${selectItemPos - (selectOptionsHeight - selectItemPos)}px` : customMaxHeightValue;
                    } else {
                        selectItem.classList.remove("select--show-top");
                        selectItemScroll.style.maxHeight = `${newMaxHeightValue}px`;
                    }
                }
            } else setTimeout((() => {
                selectItem.classList.remove("select--show-top");
                selectItemScroll.style.maxHeight = customMaxHeightValue;
            }), +originalSelect.dataset.speed);
        }
        optionAction(selectItem, originalSelect, optionItem) {
            const selectOptions = selectItem.querySelector(`${this.getSelectClass(this.selectClasses.classSelectOptions)}`);
            if (!selectOptions.classList.contains("_slide")) {
                if (originalSelect.multiple) {
                    optionItem.classList.toggle(this.selectClasses.classSelectOptionSelected);
                    const originalSelectSelectedItems = this.getSelectedOptionsData(originalSelect).elements;
                    originalSelectSelectedItems.forEach((originalSelectSelectedItem => {
                        originalSelectSelectedItem.removeAttribute("selected");
                    }));
                    const selectSelectedItems = selectItem.querySelectorAll(this.getSelectClass(this.selectClasses.classSelectOptionSelected));
                    selectSelectedItems.forEach((selectSelectedItems => {
                        originalSelect.querySelector(`option[value = "${selectSelectedItems.dataset.value}"]`).setAttribute("selected", "selected");
                    }));
                } else {
                    if (!originalSelect.hasAttribute("data-show-selected")) setTimeout((() => {
                        if (selectItem.querySelector(`${this.getSelectClass(this.selectClasses.classSelectOption)}[hidden]`)) selectItem.querySelector(`${this.getSelectClass(this.selectClasses.classSelectOption)}[hidden]`).hidden = false;
                        optionItem.hidden = true;
                    }), this.config.speed);
                    originalSelect.value = optionItem.hasAttribute("data-value") ? optionItem.dataset.value : optionItem.textContent;
                    this.selectAction(selectItem);
                }
                this.setSelectTitleValue(selectItem, originalSelect);
                this.setSelectChange(originalSelect);
            }
        }
        selectChange(e) {
            const originalSelect = e.target;
            this.selectBuild(originalSelect);
            this.setSelectChange(originalSelect);
        }
        setSelectChange(originalSelect) {
            if (originalSelect.hasAttribute("data-validate")) formValidate.validateInput(originalSelect);
            if (originalSelect.hasAttribute("data-submit") && originalSelect.value) {
                let tempButton = document.createElement("button");
                tempButton.type = "submit";
                originalSelect.closest("form").append(tempButton);
                tempButton.click();
                tempButton.remove();
            }
            const selectItem = originalSelect.parentElement;
            this.selectCallback(selectItem, originalSelect);
        }
        selectDisabled(selectItem, originalSelect) {
            if (originalSelect.disabled) {
                selectItem.classList.add(this.selectClasses.classSelectDisabled);
                this.getSelectElement(selectItem, this.selectClasses.classSelectTitle).selectElement.disabled = true;
            } else {
                selectItem.classList.remove(this.selectClasses.classSelectDisabled);
                this.getSelectElement(selectItem, this.selectClasses.classSelectTitle).selectElement.disabled = false;
            }
        }
        searchActions(selectItem) {
            this.getSelectElement(selectItem).originalSelect;
            const selectInput = this.getSelectElement(selectItem, this.selectClasses.classSelectInput).selectElement;
            const selectOptions = this.getSelectElement(selectItem, this.selectClasses.classSelectOptions).selectElement;
            const selectOptionsItems = selectOptions.querySelectorAll(`.${this.selectClasses.classSelectOption} `);
            const _this = this;
            selectInput.addEventListener("input", (function() {
                selectOptionsItems.forEach((selectOptionsItem => {
                    if (selectOptionsItem.textContent.toUpperCase().includes(selectInput.value.toUpperCase())) selectOptionsItem.hidden = false; else selectOptionsItem.hidden = true;
                }));
                selectOptions.hidden === true ? _this.selectAction(selectItem) : null;
            }));
        }
        selectCallback(selectItem, originalSelect) {
            document.dispatchEvent(new CustomEvent("selectCallback", {
                detail: {
                    select: originalSelect
                }
            }));
        }
        setLogging(message) {
            this.config.logging ? functions_FLS(`[select]: ${message} `) : null;
        }
    }
    modules_flsModules.select = new SelectConstructor({});
    var PipsMode;
    (function(PipsMode) {
        PipsMode["Range"] = "range";
        PipsMode["Steps"] = "steps";
        PipsMode["Positions"] = "positions";
        PipsMode["Count"] = "count";
        PipsMode["Values"] = "values";
    })(PipsMode || (PipsMode = {}));
    var PipsType;
    (function(PipsType) {
        PipsType[PipsType["None"] = -1] = "None";
        PipsType[PipsType["NoValue"] = 0] = "NoValue";
        PipsType[PipsType["LargeValue"] = 1] = "LargeValue";
        PipsType[PipsType["SmallValue"] = 2] = "SmallValue";
    })(PipsType || (PipsType = {}));
    function isValidFormatter(entry) {
        return isValidPartialFormatter(entry) && typeof entry.from === "function";
    }
    function isValidPartialFormatter(entry) {
        return typeof entry === "object" && typeof entry.to === "function";
    }
    function removeElement(el) {
        el.parentElement.removeChild(el);
    }
    function isSet(value) {
        return value !== null && value !== void 0;
    }
    function preventDefault(e) {
        e.preventDefault();
    }
    function unique(array) {
        return array.filter((function(a) {
            return !this[a] ? this[a] = true : false;
        }), {});
    }
    function closest(value, to) {
        return Math.round(value / to) * to;
    }
    function offset(elem, orientation) {
        var rect = elem.getBoundingClientRect();
        var doc = elem.ownerDocument;
        var docElem = doc.documentElement;
        var pageOffset = getPageOffset(doc);
        if (/webkit.*Chrome.*Mobile/i.test(navigator.userAgent)) pageOffset.x = 0;
        return orientation ? rect.top + pageOffset.y - docElem.clientTop : rect.left + pageOffset.x - docElem.clientLeft;
    }
    function isNumeric(a) {
        return typeof a === "number" && !isNaN(a) && isFinite(a);
    }
    function addClassFor(element, className, duration) {
        if (duration > 0) {
            addClass(element, className);
            setTimeout((function() {
                removeClass(element, className);
            }), duration);
        }
    }
    function limit(a) {
        return Math.max(Math.min(a, 100), 0);
    }
    function asArray(a) {
        return Array.isArray(a) ? a : [ a ];
    }
    function countDecimals(numStr) {
        numStr = String(numStr);
        var pieces = numStr.split(".");
        return pieces.length > 1 ? pieces[1].length : 0;
    }
    function addClass(el, className) {
        if (el.classList && !/\s/.test(className)) el.classList.add(className); else el.className += " " + className;
    }
    function removeClass(el, className) {
        if (el.classList && !/\s/.test(className)) el.classList.remove(className); else el.className = el.className.replace(new RegExp("(^|\\b)" + className.split(" ").join("|") + "(\\b|$)", "gi"), " ");
    }
    function hasClass(el, className) {
        return el.classList ? el.classList.contains(className) : new RegExp("\\b" + className + "\\b").test(el.className);
    }
    function getPageOffset(doc) {
        var supportPageOffset = window.pageXOffset !== void 0;
        var isCSS1Compat = (doc.compatMode || "") === "CSS1Compat";
        var x = supportPageOffset ? window.pageXOffset : isCSS1Compat ? doc.documentElement.scrollLeft : doc.body.scrollLeft;
        var y = supportPageOffset ? window.pageYOffset : isCSS1Compat ? doc.documentElement.scrollTop : doc.body.scrollTop;
        return {
            x,
            y
        };
    }
    function getActions() {
        return window.navigator.pointerEnabled ? {
            start: "pointerdown",
            move: "pointermove",
            end: "pointerup"
        } : window.navigator.msPointerEnabled ? {
            start: "MSPointerDown",
            move: "MSPointerMove",
            end: "MSPointerUp"
        } : {
            start: "mousedown touchstart",
            move: "mousemove touchmove",
            end: "mouseup touchend"
        };
    }
    function getSupportsPassive() {
        var supportsPassive = false;
        try {
            var opts = Object.defineProperty({}, "passive", {
                get: function() {
                    supportsPassive = true;
                }
            });
            window.addEventListener("test", null, opts);
        } catch (e) {}
        return supportsPassive;
    }
    function getSupportsTouchActionNone() {
        return window.CSS && CSS.supports && CSS.supports("touch-action", "none");
    }
    function subRangeRatio(pa, pb) {
        return 100 / (pb - pa);
    }
    function fromPercentage(range, value, startRange) {
        return value * 100 / (range[startRange + 1] - range[startRange]);
    }
    function toPercentage(range, value) {
        return fromPercentage(range, range[0] < 0 ? value + Math.abs(range[0]) : value - range[0], 0);
    }
    function isPercentage(range, value) {
        return value * (range[1] - range[0]) / 100 + range[0];
    }
    function getJ(value, arr) {
        var j = 1;
        while (value >= arr[j]) j += 1;
        return j;
    }
    function toStepping(xVal, xPct, value) {
        if (value >= xVal.slice(-1)[0]) return 100;
        var j = getJ(value, xVal);
        var va = xVal[j - 1];
        var vb = xVal[j];
        var pa = xPct[j - 1];
        var pb = xPct[j];
        return pa + toPercentage([ va, vb ], value) / subRangeRatio(pa, pb);
    }
    function fromStepping(xVal, xPct, value) {
        if (value >= 100) return xVal.slice(-1)[0];
        var j = getJ(value, xPct);
        var va = xVal[j - 1];
        var vb = xVal[j];
        var pa = xPct[j - 1];
        var pb = xPct[j];
        return isPercentage([ va, vb ], (value - pa) * subRangeRatio(pa, pb));
    }
    function getStep(xPct, xSteps, snap, value) {
        if (value === 100) return value;
        var j = getJ(value, xPct);
        var a = xPct[j - 1];
        var b = xPct[j];
        if (snap) {
            if (value - a > (b - a) / 2) return b;
            return a;
        }
        if (!xSteps[j - 1]) return value;
        return xPct[j - 1] + closest(value - xPct[j - 1], xSteps[j - 1]);
    }
    var Spectrum = function() {
        function Spectrum(entry, snap, singleStep) {
            this.xPct = [];
            this.xVal = [];
            this.xSteps = [];
            this.xNumSteps = [];
            this.xHighestCompleteStep = [];
            this.xSteps = [ singleStep || false ];
            this.xNumSteps = [ false ];
            this.snap = snap;
            var index;
            var ordered = [];
            Object.keys(entry).forEach((function(index) {
                ordered.push([ asArray(entry[index]), index ]);
            }));
            ordered.sort((function(a, b) {
                return a[0][0] - b[0][0];
            }));
            for (index = 0; index < ordered.length; index++) this.handleEntryPoint(ordered[index][1], ordered[index][0]);
            this.xNumSteps = this.xSteps.slice(0);
            for (index = 0; index < this.xNumSteps.length; index++) this.handleStepPoint(index, this.xNumSteps[index]);
        }
        Spectrum.prototype.getDistance = function(value) {
            var distances = [];
            for (var index = 0; index < this.xNumSteps.length - 1; index++) distances[index] = fromPercentage(this.xVal, value, index);
            return distances;
        };
        Spectrum.prototype.getAbsoluteDistance = function(value, distances, direction) {
            var xPct_index = 0;
            if (value < this.xPct[this.xPct.length - 1]) while (value > this.xPct[xPct_index + 1]) xPct_index++; else if (value === this.xPct[this.xPct.length - 1]) xPct_index = this.xPct.length - 2;
            if (!direction && value === this.xPct[xPct_index + 1]) xPct_index++;
            if (distances === null) distances = [];
            var start_factor;
            var rest_factor = 1;
            var rest_rel_distance = distances[xPct_index];
            var range_pct = 0;
            var rel_range_distance = 0;
            var abs_distance_counter = 0;
            var range_counter = 0;
            if (direction) start_factor = (value - this.xPct[xPct_index]) / (this.xPct[xPct_index + 1] - this.xPct[xPct_index]); else start_factor = (this.xPct[xPct_index + 1] - value) / (this.xPct[xPct_index + 1] - this.xPct[xPct_index]);
            while (rest_rel_distance > 0) {
                range_pct = this.xPct[xPct_index + 1 + range_counter] - this.xPct[xPct_index + range_counter];
                if (distances[xPct_index + range_counter] * rest_factor + 100 - start_factor * 100 > 100) {
                    rel_range_distance = range_pct * start_factor;
                    rest_factor = (rest_rel_distance - 100 * start_factor) / distances[xPct_index + range_counter];
                    start_factor = 1;
                } else {
                    rel_range_distance = distances[xPct_index + range_counter] * range_pct / 100 * rest_factor;
                    rest_factor = 0;
                }
                if (direction) {
                    abs_distance_counter -= rel_range_distance;
                    if (this.xPct.length + range_counter >= 1) range_counter--;
                } else {
                    abs_distance_counter += rel_range_distance;
                    if (this.xPct.length - range_counter >= 1) range_counter++;
                }
                rest_rel_distance = distances[xPct_index + range_counter] * rest_factor;
            }
            return value + abs_distance_counter;
        };
        Spectrum.prototype.toStepping = function(value) {
            value = toStepping(this.xVal, this.xPct, value);
            return value;
        };
        Spectrum.prototype.fromStepping = function(value) {
            return fromStepping(this.xVal, this.xPct, value);
        };
        Spectrum.prototype.getStep = function(value) {
            value = getStep(this.xPct, this.xSteps, this.snap, value);
            return value;
        };
        Spectrum.prototype.getDefaultStep = function(value, isDown, size) {
            var j = getJ(value, this.xPct);
            if (value === 100 || isDown && value === this.xPct[j - 1]) j = Math.max(j - 1, 1);
            return (this.xVal[j] - this.xVal[j - 1]) / size;
        };
        Spectrum.prototype.getNearbySteps = function(value) {
            var j = getJ(value, this.xPct);
            return {
                stepBefore: {
                    startValue: this.xVal[j - 2],
                    step: this.xNumSteps[j - 2],
                    highestStep: this.xHighestCompleteStep[j - 2]
                },
                thisStep: {
                    startValue: this.xVal[j - 1],
                    step: this.xNumSteps[j - 1],
                    highestStep: this.xHighestCompleteStep[j - 1]
                },
                stepAfter: {
                    startValue: this.xVal[j],
                    step: this.xNumSteps[j],
                    highestStep: this.xHighestCompleteStep[j]
                }
            };
        };
        Spectrum.prototype.countStepDecimals = function() {
            var stepDecimals = this.xNumSteps.map(countDecimals);
            return Math.max.apply(null, stepDecimals);
        };
        Spectrum.prototype.hasNoSize = function() {
            return this.xVal[0] === this.xVal[this.xVal.length - 1];
        };
        Spectrum.prototype.convert = function(value) {
            return this.getStep(this.toStepping(value));
        };
        Spectrum.prototype.handleEntryPoint = function(index, value) {
            var percentage;
            if (index === "min") percentage = 0; else if (index === "max") percentage = 100; else percentage = parseFloat(index);
            if (!isNumeric(percentage) || !isNumeric(value[0])) throw new Error("noUiSlider: 'range' value isn't numeric.");
            this.xPct.push(percentage);
            this.xVal.push(value[0]);
            var value1 = Number(value[1]);
            if (!percentage) {
                if (!isNaN(value1)) this.xSteps[0] = value1;
            } else this.xSteps.push(isNaN(value1) ? false : value1);
            this.xHighestCompleteStep.push(0);
        };
        Spectrum.prototype.handleStepPoint = function(i, n) {
            if (!n) return;
            if (this.xVal[i] === this.xVal[i + 1]) {
                this.xSteps[i] = this.xHighestCompleteStep[i] = this.xVal[i];
                return;
            }
            this.xSteps[i] = fromPercentage([ this.xVal[i], this.xVal[i + 1] ], n, 0) / subRangeRatio(this.xPct[i], this.xPct[i + 1]);
            var totalSteps = (this.xVal[i + 1] - this.xVal[i]) / this.xNumSteps[i];
            var highestStep = Math.ceil(Number(totalSteps.toFixed(3)) - 1);
            var step = this.xVal[i] + this.xNumSteps[i] * highestStep;
            this.xHighestCompleteStep[i] = step;
        };
        return Spectrum;
    }();
    var defaultFormatter = {
        to: function(value) {
            return value === void 0 ? "" : value.toFixed(2);
        },
        from: Number
    };
    var cssClasses = {
        target: "target",
        base: "base",
        origin: "origin",
        handle: "handle",
        handleLower: "handle-lower",
        handleUpper: "handle-upper",
        touchArea: "touch-area",
        horizontal: "horizontal",
        vertical: "vertical",
        background: "background",
        connect: "connect",
        connects: "connects",
        ltr: "ltr",
        rtl: "rtl",
        textDirectionLtr: "txt-dir-ltr",
        textDirectionRtl: "txt-dir-rtl",
        draggable: "draggable",
        drag: "state-drag",
        tap: "state-tap",
        active: "active",
        tooltip: "tooltip",
        pips: "pips",
        pipsHorizontal: "pips-horizontal",
        pipsVertical: "pips-vertical",
        marker: "marker",
        markerHorizontal: "marker-horizontal",
        markerVertical: "marker-vertical",
        markerNormal: "marker-normal",
        markerLarge: "marker-large",
        markerSub: "marker-sub",
        value: "value",
        valueHorizontal: "value-horizontal",
        valueVertical: "value-vertical",
        valueNormal: "value-normal",
        valueLarge: "value-large",
        valueSub: "value-sub"
    };
    var INTERNAL_EVENT_NS = {
        tooltips: ".__tooltips",
        aria: ".__aria"
    };
    function testStep(parsed, entry) {
        if (!isNumeric(entry)) throw new Error("noUiSlider: 'step' is not numeric.");
        parsed.singleStep = entry;
    }
    function testKeyboardPageMultiplier(parsed, entry) {
        if (!isNumeric(entry)) throw new Error("noUiSlider: 'keyboardPageMultiplier' is not numeric.");
        parsed.keyboardPageMultiplier = entry;
    }
    function testKeyboardMultiplier(parsed, entry) {
        if (!isNumeric(entry)) throw new Error("noUiSlider: 'keyboardMultiplier' is not numeric.");
        parsed.keyboardMultiplier = entry;
    }
    function testKeyboardDefaultStep(parsed, entry) {
        if (!isNumeric(entry)) throw new Error("noUiSlider: 'keyboardDefaultStep' is not numeric.");
        parsed.keyboardDefaultStep = entry;
    }
    function testRange(parsed, entry) {
        if (typeof entry !== "object" || Array.isArray(entry)) throw new Error("noUiSlider: 'range' is not an object.");
        if (entry.min === void 0 || entry.max === void 0) throw new Error("noUiSlider: Missing 'min' or 'max' in 'range'.");
        parsed.spectrum = new Spectrum(entry, parsed.snap || false, parsed.singleStep);
    }
    function testStart(parsed, entry) {
        entry = asArray(entry);
        if (!Array.isArray(entry) || !entry.length) throw new Error("noUiSlider: 'start' option is incorrect.");
        parsed.handles = entry.length;
        parsed.start = entry;
    }
    function testSnap(parsed, entry) {
        if (typeof entry !== "boolean") throw new Error("noUiSlider: 'snap' option must be a boolean.");
        parsed.snap = entry;
    }
    function testAnimate(parsed, entry) {
        if (typeof entry !== "boolean") throw new Error("noUiSlider: 'animate' option must be a boolean.");
        parsed.animate = entry;
    }
    function testAnimationDuration(parsed, entry) {
        if (typeof entry !== "number") throw new Error("noUiSlider: 'animationDuration' option must be a number.");
        parsed.animationDuration = entry;
    }
    function testConnect(parsed, entry) {
        var connect = [ false ];
        var i;
        if (entry === "lower") entry = [ true, false ]; else if (entry === "upper") entry = [ false, true ];
        if (entry === true || entry === false) {
            for (i = 1; i < parsed.handles; i++) connect.push(entry);
            connect.push(false);
        } else if (!Array.isArray(entry) || !entry.length || entry.length !== parsed.handles + 1) throw new Error("noUiSlider: 'connect' option doesn't match handle count."); else connect = entry;
        parsed.connect = connect;
    }
    function testOrientation(parsed, entry) {
        switch (entry) {
          case "horizontal":
            parsed.ort = 0;
            break;

          case "vertical":
            parsed.ort = 1;
            break;

          default:
            throw new Error("noUiSlider: 'orientation' option is invalid.");
        }
    }
    function testMargin(parsed, entry) {
        if (!isNumeric(entry)) throw new Error("noUiSlider: 'margin' option must be numeric.");
        if (entry === 0) return;
        parsed.margin = parsed.spectrum.getDistance(entry);
    }
    function testLimit(parsed, entry) {
        if (!isNumeric(entry)) throw new Error("noUiSlider: 'limit' option must be numeric.");
        parsed.limit = parsed.spectrum.getDistance(entry);
        if (!parsed.limit || parsed.handles < 2) throw new Error("noUiSlider: 'limit' option is only supported on linear sliders with 2 or more handles.");
    }
    function testPadding(parsed, entry) {
        var index;
        if (!isNumeric(entry) && !Array.isArray(entry)) throw new Error("noUiSlider: 'padding' option must be numeric or array of exactly 2 numbers.");
        if (Array.isArray(entry) && !(entry.length === 2 || isNumeric(entry[0]) || isNumeric(entry[1]))) throw new Error("noUiSlider: 'padding' option must be numeric or array of exactly 2 numbers.");
        if (entry === 0) return;
        if (!Array.isArray(entry)) entry = [ entry, entry ];
        parsed.padding = [ parsed.spectrum.getDistance(entry[0]), parsed.spectrum.getDistance(entry[1]) ];
        for (index = 0; index < parsed.spectrum.xNumSteps.length - 1; index++) if (parsed.padding[0][index] < 0 || parsed.padding[1][index] < 0) throw new Error("noUiSlider: 'padding' option must be a positive number(s).");
        var totalPadding = entry[0] + entry[1];
        var firstValue = parsed.spectrum.xVal[0];
        var lastValue = parsed.spectrum.xVal[parsed.spectrum.xVal.length - 1];
        if (totalPadding / (lastValue - firstValue) > 1) throw new Error("noUiSlider: 'padding' option must not exceed 100% of the range.");
    }
    function testDirection(parsed, entry) {
        switch (entry) {
          case "ltr":
            parsed.dir = 0;
            break;

          case "rtl":
            parsed.dir = 1;
            break;

          default:
            throw new Error("noUiSlider: 'direction' option was not recognized.");
        }
    }
    function testBehaviour(parsed, entry) {
        if (typeof entry !== "string") throw new Error("noUiSlider: 'behaviour' must be a string containing options.");
        var tap = entry.indexOf("tap") >= 0;
        var drag = entry.indexOf("drag") >= 0;
        var fixed = entry.indexOf("fixed") >= 0;
        var snap = entry.indexOf("snap") >= 0;
        var hover = entry.indexOf("hover") >= 0;
        var unconstrained = entry.indexOf("unconstrained") >= 0;
        var invertConnects = entry.indexOf("invert-connects") >= 0;
        var dragAll = entry.indexOf("drag-all") >= 0;
        var smoothSteps = entry.indexOf("smooth-steps") >= 0;
        if (fixed) {
            if (parsed.handles !== 2) throw new Error("noUiSlider: 'fixed' behaviour must be used with 2 handles");
            testMargin(parsed, parsed.start[1] - parsed.start[0]);
        }
        if (invertConnects && parsed.handles !== 2) throw new Error("noUiSlider: 'invert-connects' behaviour must be used with 2 handles");
        if (unconstrained && (parsed.margin || parsed.limit)) throw new Error("noUiSlider: 'unconstrained' behaviour cannot be used with margin or limit");
        parsed.events = {
            tap: tap || snap,
            drag,
            dragAll,
            smoothSteps,
            fixed,
            snap,
            hover,
            unconstrained,
            invertConnects
        };
    }
    function testTooltips(parsed, entry) {
        if (entry === false) return;
        if (entry === true || isValidPartialFormatter(entry)) {
            parsed.tooltips = [];
            for (var i = 0; i < parsed.handles; i++) parsed.tooltips.push(entry);
        } else {
            entry = asArray(entry);
            if (entry.length !== parsed.handles) throw new Error("noUiSlider: must pass a formatter for all handles.");
            entry.forEach((function(formatter) {
                if (typeof formatter !== "boolean" && !isValidPartialFormatter(formatter)) throw new Error("noUiSlider: 'tooltips' must be passed a formatter or 'false'.");
            }));
            parsed.tooltips = entry;
        }
    }
    function testHandleAttributes(parsed, entry) {
        if (entry.length !== parsed.handles) throw new Error("noUiSlider: must pass a attributes for all handles.");
        parsed.handleAttributes = entry;
    }
    function testAriaFormat(parsed, entry) {
        if (!isValidPartialFormatter(entry)) throw new Error("noUiSlider: 'ariaFormat' requires 'to' method.");
        parsed.ariaFormat = entry;
    }
    function testFormat(parsed, entry) {
        if (!isValidFormatter(entry)) throw new Error("noUiSlider: 'format' requires 'to' and 'from' methods.");
        parsed.format = entry;
    }
    function testKeyboardSupport(parsed, entry) {
        if (typeof entry !== "boolean") throw new Error("noUiSlider: 'keyboardSupport' option must be a boolean.");
        parsed.keyboardSupport = entry;
    }
    function testDocumentElement(parsed, entry) {
        parsed.documentElement = entry;
    }
    function testCssPrefix(parsed, entry) {
        if (typeof entry !== "string" && entry !== false) throw new Error("noUiSlider: 'cssPrefix' must be a string or `false`.");
        parsed.cssPrefix = entry;
    }
    function testCssClasses(parsed, entry) {
        if (typeof entry !== "object") throw new Error("noUiSlider: 'cssClasses' must be an object.");
        if (typeof parsed.cssPrefix === "string") {
            parsed.cssClasses = {};
            Object.keys(entry).forEach((function(key) {
                parsed.cssClasses[key] = parsed.cssPrefix + entry[key];
            }));
        } else parsed.cssClasses = entry;
    }
    function testOptions(options) {
        var parsed = {
            margin: null,
            limit: null,
            padding: null,
            animate: true,
            animationDuration: 300,
            ariaFormat: defaultFormatter,
            format: defaultFormatter
        };
        var tests = {
            step: {
                r: false,
                t: testStep
            },
            keyboardPageMultiplier: {
                r: false,
                t: testKeyboardPageMultiplier
            },
            keyboardMultiplier: {
                r: false,
                t: testKeyboardMultiplier
            },
            keyboardDefaultStep: {
                r: false,
                t: testKeyboardDefaultStep
            },
            start: {
                r: true,
                t: testStart
            },
            connect: {
                r: true,
                t: testConnect
            },
            direction: {
                r: true,
                t: testDirection
            },
            snap: {
                r: false,
                t: testSnap
            },
            animate: {
                r: false,
                t: testAnimate
            },
            animationDuration: {
                r: false,
                t: testAnimationDuration
            },
            range: {
                r: true,
                t: testRange
            },
            orientation: {
                r: false,
                t: testOrientation
            },
            margin: {
                r: false,
                t: testMargin
            },
            limit: {
                r: false,
                t: testLimit
            },
            padding: {
                r: false,
                t: testPadding
            },
            behaviour: {
                r: true,
                t: testBehaviour
            },
            ariaFormat: {
                r: false,
                t: testAriaFormat
            },
            format: {
                r: false,
                t: testFormat
            },
            tooltips: {
                r: false,
                t: testTooltips
            },
            keyboardSupport: {
                r: true,
                t: testKeyboardSupport
            },
            documentElement: {
                r: false,
                t: testDocumentElement
            },
            cssPrefix: {
                r: true,
                t: testCssPrefix
            },
            cssClasses: {
                r: true,
                t: testCssClasses
            },
            handleAttributes: {
                r: false,
                t: testHandleAttributes
            }
        };
        var defaults = {
            connect: false,
            direction: "ltr",
            behaviour: "tap",
            orientation: "horizontal",
            keyboardSupport: true,
            cssPrefix: "noUi-",
            cssClasses,
            keyboardPageMultiplier: 5,
            keyboardMultiplier: 1,
            keyboardDefaultStep: 10
        };
        if (options.format && !options.ariaFormat) options.ariaFormat = options.format;
        Object.keys(tests).forEach((function(name) {
            if (!isSet(options[name]) && defaults[name] === void 0) {
                if (tests[name].r) throw new Error("noUiSlider: '" + name + "' is required.");
                return;
            }
            tests[name].t(parsed, !isSet(options[name]) ? defaults[name] : options[name]);
        }));
        parsed.pips = options.pips;
        var d = document.createElement("div");
        var msPrefix = d.style.msTransform !== void 0;
        var noPrefix = d.style.transform !== void 0;
        parsed.transformRule = noPrefix ? "transform" : msPrefix ? "msTransform" : "webkitTransform";
        var styles = [ [ "left", "top" ], [ "right", "bottom" ] ];
        parsed.style = styles[parsed.dir][parsed.ort];
        return parsed;
    }
    function scope(target, options, originalOptions) {
        var actions = getActions();
        var supportsTouchActionNone = getSupportsTouchActionNone();
        var supportsPassive = supportsTouchActionNone && getSupportsPassive();
        var scope_Target = target;
        var scope_Base;
        var scope_ConnectBase;
        var scope_Handles;
        var scope_Connects;
        var scope_Pips;
        var scope_Tooltips;
        var scope_Spectrum = options.spectrum;
        var scope_Values = [];
        var scope_Locations = [];
        var scope_HandleNumbers = [];
        var scope_ActiveHandlesCount = 0;
        var scope_Events = {};
        var scope_ConnectsInverted = false;
        var scope_Document = target.ownerDocument;
        var scope_DocumentElement = options.documentElement || scope_Document.documentElement;
        var scope_Body = scope_Document.body;
        var scope_DirOffset = scope_Document.dir === "rtl" || options.ort === 1 ? 0 : 100;
        function addNodeTo(addTarget, className) {
            var div = scope_Document.createElement("div");
            if (className) addClass(div, className);
            addTarget.appendChild(div);
            return div;
        }
        function addOrigin(base, handleNumber) {
            var origin = addNodeTo(base, options.cssClasses.origin);
            var handle = addNodeTo(origin, options.cssClasses.handle);
            addNodeTo(handle, options.cssClasses.touchArea);
            handle.setAttribute("data-handle", String(handleNumber));
            if (options.keyboardSupport) {
                handle.setAttribute("tabindex", "0");
                handle.addEventListener("keydown", (function(event) {
                    return eventKeydown(event, handleNumber);
                }));
            }
            if (options.handleAttributes !== void 0) {
                var attributes_1 = options.handleAttributes[handleNumber];
                Object.keys(attributes_1).forEach((function(attribute) {
                    handle.setAttribute(attribute, attributes_1[attribute]);
                }));
            }
            handle.setAttribute("role", "slider");
            handle.setAttribute("aria-orientation", options.ort ? "vertical" : "horizontal");
            if (handleNumber === 0) addClass(handle, options.cssClasses.handleLower); else if (handleNumber === options.handles - 1) addClass(handle, options.cssClasses.handleUpper);
            origin.handle = handle;
            return origin;
        }
        function addConnect(base, add) {
            if (!add) return false;
            return addNodeTo(base, options.cssClasses.connect);
        }
        function addElements(connectOptions, base) {
            scope_ConnectBase = addNodeTo(base, options.cssClasses.connects);
            scope_Handles = [];
            scope_Connects = [];
            scope_Connects.push(addConnect(scope_ConnectBase, connectOptions[0]));
            for (var i = 0; i < options.handles; i++) {
                scope_Handles.push(addOrigin(base, i));
                scope_HandleNumbers[i] = i;
                scope_Connects.push(addConnect(scope_ConnectBase, connectOptions[i + 1]));
            }
        }
        function addSlider(addTarget) {
            addClass(addTarget, options.cssClasses.target);
            if (options.dir === 0) addClass(addTarget, options.cssClasses.ltr); else addClass(addTarget, options.cssClasses.rtl);
            if (options.ort === 0) addClass(addTarget, options.cssClasses.horizontal); else addClass(addTarget, options.cssClasses.vertical);
            var textDirection = getComputedStyle(addTarget).direction;
            if (textDirection === "rtl") addClass(addTarget, options.cssClasses.textDirectionRtl); else addClass(addTarget, options.cssClasses.textDirectionLtr);
            return addNodeTo(addTarget, options.cssClasses.base);
        }
        function addTooltip(handle, handleNumber) {
            if (!options.tooltips || !options.tooltips[handleNumber]) return false;
            return addNodeTo(handle.firstChild, options.cssClasses.tooltip);
        }
        function isSliderDisabled() {
            return scope_Target.hasAttribute("disabled");
        }
        function isHandleDisabled(handleNumber) {
            var handleOrigin = scope_Handles[handleNumber];
            return handleOrigin.hasAttribute("disabled");
        }
        function disable(handleNumber) {
            if (handleNumber !== null && handleNumber !== void 0) {
                scope_Handles[handleNumber].setAttribute("disabled", "");
                scope_Handles[handleNumber].handle.removeAttribute("tabindex");
            } else {
                scope_Target.setAttribute("disabled", "");
                scope_Handles.forEach((function(handle) {
                    handle.handle.removeAttribute("tabindex");
                }));
            }
        }
        function enable(handleNumber) {
            if (handleNumber !== null && handleNumber !== void 0) {
                scope_Handles[handleNumber].removeAttribute("disabled");
                scope_Handles[handleNumber].handle.setAttribute("tabindex", "0");
            } else {
                scope_Target.removeAttribute("disabled");
                scope_Handles.forEach((function(handle) {
                    handle.removeAttribute("disabled");
                    handle.handle.setAttribute("tabindex", "0");
                }));
            }
        }
        function removeTooltips() {
            if (scope_Tooltips) {
                removeEvent("update" + INTERNAL_EVENT_NS.tooltips);
                scope_Tooltips.forEach((function(tooltip) {
                    if (tooltip) removeElement(tooltip);
                }));
                scope_Tooltips = null;
            }
        }
        function tooltips() {
            removeTooltips();
            scope_Tooltips = scope_Handles.map(addTooltip);
            bindEvent("update" + INTERNAL_EVENT_NS.tooltips, (function(values, handleNumber, unencoded) {
                if (!scope_Tooltips || !options.tooltips) return;
                if (scope_Tooltips[handleNumber] === false) return;
                var formattedValue = values[handleNumber];
                if (options.tooltips[handleNumber] !== true) formattedValue = options.tooltips[handleNumber].to(unencoded[handleNumber]);
                scope_Tooltips[handleNumber].innerHTML = formattedValue;
            }));
        }
        function aria() {
            removeEvent("update" + INTERNAL_EVENT_NS.aria);
            bindEvent("update" + INTERNAL_EVENT_NS.aria, (function(values, handleNumber, unencoded, tap, positions) {
                scope_HandleNumbers.forEach((function(index) {
                    var handle = scope_Handles[index];
                    var min = checkHandlePosition(scope_Locations, index, 0, true, true, true);
                    var max = checkHandlePosition(scope_Locations, index, 100, true, true, true);
                    var now = positions[index];
                    var text = String(options.ariaFormat.to(unencoded[index]));
                    min = scope_Spectrum.fromStepping(min).toFixed(1);
                    max = scope_Spectrum.fromStepping(max).toFixed(1);
                    now = scope_Spectrum.fromStepping(now).toFixed(1);
                    handle.children[0].setAttribute("aria-valuemin", min);
                    handle.children[0].setAttribute("aria-valuemax", max);
                    handle.children[0].setAttribute("aria-valuenow", now);
                    handle.children[0].setAttribute("aria-valuetext", text);
                }));
            }));
        }
        function getGroup(pips) {
            if (pips.mode === PipsMode.Range || pips.mode === PipsMode.Steps) return scope_Spectrum.xVal;
            if (pips.mode === PipsMode.Count) {
                if (pips.values < 2) throw new Error("noUiSlider: 'values' (>= 2) required for mode 'count'.");
                var interval = pips.values - 1;
                var spread = 100 / interval;
                var values = [];
                while (interval--) values[interval] = interval * spread;
                values.push(100);
                return mapToRange(values, pips.stepped);
            }
            if (pips.mode === PipsMode.Positions) return mapToRange(pips.values, pips.stepped);
            if (pips.mode === PipsMode.Values) {
                if (pips.stepped) return pips.values.map((function(value) {
                    return scope_Spectrum.fromStepping(scope_Spectrum.getStep(scope_Spectrum.toStepping(value)));
                }));
                return pips.values;
            }
            return [];
        }
        function mapToRange(values, stepped) {
            return values.map((function(value) {
                return scope_Spectrum.fromStepping(stepped ? scope_Spectrum.getStep(value) : value);
            }));
        }
        function generateSpread(pips) {
            function safeIncrement(value, increment) {
                return Number((value + increment).toFixed(7));
            }
            var group = getGroup(pips);
            var indexes = {};
            var firstInRange = scope_Spectrum.xVal[0];
            var lastInRange = scope_Spectrum.xVal[scope_Spectrum.xVal.length - 1];
            var ignoreFirst = false;
            var ignoreLast = false;
            var prevPct = 0;
            group = unique(group.slice().sort((function(a, b) {
                return a - b;
            })));
            if (group[0] !== firstInRange) {
                group.unshift(firstInRange);
                ignoreFirst = true;
            }
            if (group[group.length - 1] !== lastInRange) {
                group.push(lastInRange);
                ignoreLast = true;
            }
            group.forEach((function(current, index) {
                var step;
                var i;
                var q;
                var low = current;
                var high = group[index + 1];
                var newPct;
                var pctDifference;
                var pctPos;
                var type;
                var steps;
                var realSteps;
                var stepSize;
                var isSteps = pips.mode === PipsMode.Steps;
                if (isSteps) step = scope_Spectrum.xNumSteps[index];
                if (!step) step = high - low;
                if (high === void 0) high = low;
                step = Math.max(step, 1e-7);
                for (i = low; i <= high; i = safeIncrement(i, step)) {
                    newPct = scope_Spectrum.toStepping(i);
                    pctDifference = newPct - prevPct;
                    steps = pctDifference / (pips.density || 1);
                    realSteps = Math.round(steps);
                    stepSize = pctDifference / realSteps;
                    for (q = 1; q <= realSteps; q += 1) {
                        pctPos = prevPct + q * stepSize;
                        indexes[pctPos.toFixed(5)] = [ scope_Spectrum.fromStepping(pctPos), 0 ];
                    }
                    type = group.indexOf(i) > -1 ? PipsType.LargeValue : isSteps ? PipsType.SmallValue : PipsType.NoValue;
                    if (!index && ignoreFirst && i !== high) type = 0;
                    if (!(i === high && ignoreLast)) indexes[newPct.toFixed(5)] = [ i, type ];
                    prevPct = newPct;
                }
            }));
            return indexes;
        }
        function addMarking(spread, filterFunc, formatter) {
            var _a, _b;
            var element = scope_Document.createElement("div");
            var valueSizeClasses = (_a = {}, _a[PipsType.None] = "", _a[PipsType.NoValue] = options.cssClasses.valueNormal, 
            _a[PipsType.LargeValue] = options.cssClasses.valueLarge, _a[PipsType.SmallValue] = options.cssClasses.valueSub, 
            _a);
            var markerSizeClasses = (_b = {}, _b[PipsType.None] = "", _b[PipsType.NoValue] = options.cssClasses.markerNormal, 
            _b[PipsType.LargeValue] = options.cssClasses.markerLarge, _b[PipsType.SmallValue] = options.cssClasses.markerSub, 
            _b);
            var valueOrientationClasses = [ options.cssClasses.valueHorizontal, options.cssClasses.valueVertical ];
            var markerOrientationClasses = [ options.cssClasses.markerHorizontal, options.cssClasses.markerVertical ];
            addClass(element, options.cssClasses.pips);
            addClass(element, options.ort === 0 ? options.cssClasses.pipsHorizontal : options.cssClasses.pipsVertical);
            function getClasses(type, source) {
                var a = source === options.cssClasses.value;
                var orientationClasses = a ? valueOrientationClasses : markerOrientationClasses;
                var sizeClasses = a ? valueSizeClasses : markerSizeClasses;
                return source + " " + orientationClasses[options.ort] + " " + sizeClasses[type];
            }
            function addSpread(offset, value, type) {
                type = filterFunc ? filterFunc(value, type) : type;
                if (type === PipsType.None) return;
                var node = addNodeTo(element, false);
                node.className = getClasses(type, options.cssClasses.marker);
                node.style[options.style] = offset + "%";
                if (type > PipsType.NoValue) {
                    node = addNodeTo(element, false);
                    node.className = getClasses(type, options.cssClasses.value);
                    node.setAttribute("data-value", String(value));
                    node.style[options.style] = offset + "%";
                    node.innerHTML = String(formatter.to(value));
                }
            }
            Object.keys(spread).forEach((function(offset) {
                addSpread(offset, spread[offset][0], spread[offset][1]);
            }));
            return element;
        }
        function removePips() {
            if (scope_Pips) {
                removeElement(scope_Pips);
                scope_Pips = null;
            }
        }
        function pips(pips) {
            removePips();
            var spread = generateSpread(pips);
            var filter = pips.filter;
            var format = pips.format || {
                to: function(value) {
                    return String(Math.round(value));
                }
            };
            scope_Pips = scope_Target.appendChild(addMarking(spread, filter, format));
            return scope_Pips;
        }
        function baseSize() {
            var rect = scope_Base.getBoundingClientRect();
            var alt = "offset" + [ "Width", "Height" ][options.ort];
            return options.ort === 0 ? rect.width || scope_Base[alt] : rect.height || scope_Base[alt];
        }
        function attachEvent(events, element, callback, data) {
            var method = function(event) {
                var e = fixEvent(event, data.pageOffset, data.target || element);
                if (!e) return false;
                if (isSliderDisabled() && !data.doNotReject) return false;
                if (hasClass(scope_Target, options.cssClasses.tap) && !data.doNotReject) return false;
                if (events === actions.start && e.buttons !== void 0 && e.buttons > 1) return false;
                if (data.hover && e.buttons) return false;
                if (!supportsPassive) e.preventDefault();
                e.calcPoint = e.points[options.ort];
                callback(e, data);
                return;
            };
            var methods = [];
            events.split(" ").forEach((function(eventName) {
                element.addEventListener(eventName, method, supportsPassive ? {
                    passive: true
                } : false);
                methods.push([ eventName, method ]);
            }));
            return methods;
        }
        function fixEvent(e, pageOffset, eventTarget) {
            var touch = e.type.indexOf("touch") === 0;
            var mouse = e.type.indexOf("mouse") === 0;
            var pointer = e.type.indexOf("pointer") === 0;
            var x = 0;
            var y = 0;
            if (e.type.indexOf("MSPointer") === 0) pointer = true;
            if (e.type === "mousedown" && !e.buttons && !e.touches) return false;
            if (touch) {
                var isTouchOnTarget = function(checkTouch) {
                    var target = checkTouch.target;
                    return target === eventTarget || eventTarget.contains(target) || e.composed && e.composedPath().shift() === eventTarget;
                };
                if (e.type === "touchstart") {
                    var targetTouches = Array.prototype.filter.call(e.touches, isTouchOnTarget);
                    if (targetTouches.length > 1) return false;
                    x = targetTouches[0].pageX;
                    y = targetTouches[0].pageY;
                } else {
                    var targetTouch = Array.prototype.find.call(e.changedTouches, isTouchOnTarget);
                    if (!targetTouch) return false;
                    x = targetTouch.pageX;
                    y = targetTouch.pageY;
                }
            }
            pageOffset = pageOffset || getPageOffset(scope_Document);
            if (mouse || pointer) {
                x = e.clientX + pageOffset.x;
                y = e.clientY + pageOffset.y;
            }
            e.pageOffset = pageOffset;
            e.points = [ x, y ];
            e.cursor = mouse || pointer;
            return e;
        }
        function calcPointToPercentage(calcPoint) {
            var location = calcPoint - offset(scope_Base, options.ort);
            var proposal = location * 100 / baseSize();
            proposal = limit(proposal);
            return options.dir ? 100 - proposal : proposal;
        }
        function getClosestHandle(clickedPosition) {
            var smallestDifference = 100;
            var handleNumber = false;
            scope_Handles.forEach((function(handle, index) {
                if (isHandleDisabled(index)) return;
                var handlePosition = scope_Locations[index];
                var differenceWithThisHandle = Math.abs(handlePosition - clickedPosition);
                var clickAtEdge = differenceWithThisHandle === 100 && smallestDifference === 100;
                var isCloser = differenceWithThisHandle < smallestDifference;
                var isCloserAfter = differenceWithThisHandle <= smallestDifference && clickedPosition > handlePosition;
                if (isCloser || isCloserAfter || clickAtEdge) {
                    handleNumber = index;
                    smallestDifference = differenceWithThisHandle;
                }
            }));
            return handleNumber;
        }
        function documentLeave(event, data) {
            if (event.type === "mouseout" && event.target.nodeName === "HTML" && event.relatedTarget === null) eventEnd(event, data);
        }
        function eventMove(event, data) {
            if (navigator.appVersion.indexOf("MSIE 9") === -1 && event.buttons === 0 && data.buttonsProperty !== 0) return eventEnd(event, data);
            var movement = (options.dir ? -1 : 1) * (event.calcPoint - data.startCalcPoint);
            var proposal = movement * 100 / data.baseSize;
            moveHandles(movement > 0, proposal, data.locations, data.handleNumbers, data.connect);
        }
        function eventEnd(event, data) {
            if (data.handle) {
                removeClass(data.handle, options.cssClasses.active);
                scope_ActiveHandlesCount -= 1;
            }
            data.listeners.forEach((function(c) {
                scope_DocumentElement.removeEventListener(c[0], c[1]);
            }));
            if (scope_ActiveHandlesCount === 0) {
                removeClass(scope_Target, options.cssClasses.drag);
                setZindex();
                if (event.cursor) {
                    scope_Body.style.cursor = "";
                    scope_Body.removeEventListener("selectstart", preventDefault);
                }
            }
            if (options.events.smoothSteps) {
                data.handleNumbers.forEach((function(handleNumber) {
                    setHandle(handleNumber, scope_Locations[handleNumber], true, true, false, false);
                }));
                data.handleNumbers.forEach((function(handleNumber) {
                    fireEvent("update", handleNumber);
                }));
            }
            data.handleNumbers.forEach((function(handleNumber) {
                fireEvent("change", handleNumber);
                fireEvent("set", handleNumber);
                fireEvent("end", handleNumber);
            }));
        }
        function eventStart(event, data) {
            if (data.handleNumbers.some(isHandleDisabled)) return;
            var handle;
            if (data.handleNumbers.length === 1) {
                var handleOrigin = scope_Handles[data.handleNumbers[0]];
                handle = handleOrigin.children[0];
                scope_ActiveHandlesCount += 1;
                addClass(handle, options.cssClasses.active);
            }
            event.stopPropagation();
            var listeners = [];
            var moveEvent = attachEvent(actions.move, scope_DocumentElement, eventMove, {
                target: event.target,
                handle,
                connect: data.connect,
                listeners,
                startCalcPoint: event.calcPoint,
                baseSize: baseSize(),
                pageOffset: event.pageOffset,
                handleNumbers: data.handleNumbers,
                buttonsProperty: event.buttons,
                locations: scope_Locations.slice()
            });
            var endEvent = attachEvent(actions.end, scope_DocumentElement, eventEnd, {
                target: event.target,
                handle,
                listeners,
                doNotReject: true,
                handleNumbers: data.handleNumbers
            });
            var outEvent = attachEvent("mouseout", scope_DocumentElement, documentLeave, {
                target: event.target,
                handle,
                listeners,
                doNotReject: true,
                handleNumbers: data.handleNumbers
            });
            listeners.push.apply(listeners, moveEvent.concat(endEvent, outEvent));
            if (event.cursor) {
                scope_Body.style.cursor = getComputedStyle(event.target).cursor;
                if (scope_Handles.length > 1) addClass(scope_Target, options.cssClasses.drag);
                scope_Body.addEventListener("selectstart", preventDefault, false);
            }
            data.handleNumbers.forEach((function(handleNumber) {
                fireEvent("start", handleNumber);
            }));
        }
        function eventTap(event) {
            event.stopPropagation();
            var proposal = calcPointToPercentage(event.calcPoint);
            var handleNumber = getClosestHandle(proposal);
            if (handleNumber === false) return;
            if (!options.events.snap) addClassFor(scope_Target, options.cssClasses.tap, options.animationDuration);
            setHandle(handleNumber, proposal, true, true);
            setZindex();
            fireEvent("slide", handleNumber, true);
            fireEvent("update", handleNumber, true);
            if (!options.events.snap) {
                fireEvent("change", handleNumber, true);
                fireEvent("set", handleNumber, true);
            } else eventStart(event, {
                handleNumbers: [ handleNumber ]
            });
        }
        function eventHover(event) {
            var proposal = calcPointToPercentage(event.calcPoint);
            var to = scope_Spectrum.getStep(proposal);
            var value = scope_Spectrum.fromStepping(to);
            Object.keys(scope_Events).forEach((function(targetEvent) {
                if ("hover" === targetEvent.split(".")[0]) scope_Events[targetEvent].forEach((function(callback) {
                    callback.call(scope_Self, value);
                }));
            }));
        }
        function eventKeydown(event, handleNumber) {
            if (isSliderDisabled() || isHandleDisabled(handleNumber)) return false;
            var horizontalKeys = [ "Left", "Right" ];
            var verticalKeys = [ "Down", "Up" ];
            var largeStepKeys = [ "PageDown", "PageUp" ];
            var edgeKeys = [ "Home", "End" ];
            if (options.dir && !options.ort) horizontalKeys.reverse(); else if (options.ort && !options.dir) {
                verticalKeys.reverse();
                largeStepKeys.reverse();
            }
            var key = event.key.replace("Arrow", "");
            var isLargeDown = key === largeStepKeys[0];
            var isLargeUp = key === largeStepKeys[1];
            var isDown = key === verticalKeys[0] || key === horizontalKeys[0] || isLargeDown;
            var isUp = key === verticalKeys[1] || key === horizontalKeys[1] || isLargeUp;
            var isMin = key === edgeKeys[0];
            var isMax = key === edgeKeys[1];
            if (!isDown && !isUp && !isMin && !isMax) return true;
            event.preventDefault();
            var to;
            if (isUp || isDown) {
                var direction = isDown ? 0 : 1;
                var steps = getNextStepsForHandle(handleNumber);
                var step = steps[direction];
                if (step === null) return false;
                if (step === false) step = scope_Spectrum.getDefaultStep(scope_Locations[handleNumber], isDown, options.keyboardDefaultStep);
                if (isLargeUp || isLargeDown) step *= options.keyboardPageMultiplier; else step *= options.keyboardMultiplier;
                step = Math.max(step, 1e-7);
                step *= isDown ? -1 : 1;
                to = scope_Values[handleNumber] + step;
            } else if (isMax) to = options.spectrum.xVal[options.spectrum.xVal.length - 1]; else to = options.spectrum.xVal[0];
            setHandle(handleNumber, scope_Spectrum.toStepping(to), true, true);
            fireEvent("slide", handleNumber);
            fireEvent("update", handleNumber);
            fireEvent("change", handleNumber);
            fireEvent("set", handleNumber);
            return false;
        }
        function bindSliderEvents(behaviour) {
            if (!behaviour.fixed) scope_Handles.forEach((function(handle, index) {
                attachEvent(actions.start, handle.children[0], eventStart, {
                    handleNumbers: [ index ]
                });
            }));
            if (behaviour.tap) attachEvent(actions.start, scope_Base, eventTap, {});
            if (behaviour.hover) attachEvent(actions.move, scope_Base, eventHover, {
                hover: true
            });
            if (behaviour.drag) scope_Connects.forEach((function(connect, index) {
                if (connect === false || index === 0 || index === scope_Connects.length - 1) return;
                var handleBefore = scope_Handles[index - 1];
                var handleAfter = scope_Handles[index];
                var eventHolders = [ connect ];
                var handlesToDrag = [ handleBefore, handleAfter ];
                var handleNumbersToDrag = [ index - 1, index ];
                addClass(connect, options.cssClasses.draggable);
                if (behaviour.fixed) {
                    eventHolders.push(handleBefore.children[0]);
                    eventHolders.push(handleAfter.children[0]);
                }
                if (behaviour.dragAll) {
                    handlesToDrag = scope_Handles;
                    handleNumbersToDrag = scope_HandleNumbers;
                }
                eventHolders.forEach((function(eventHolder) {
                    attachEvent(actions.start, eventHolder, eventStart, {
                        handles: handlesToDrag,
                        handleNumbers: handleNumbersToDrag,
                        connect
                    });
                }));
            }));
        }
        function bindEvent(namespacedEvent, callback) {
            scope_Events[namespacedEvent] = scope_Events[namespacedEvent] || [];
            scope_Events[namespacedEvent].push(callback);
            if (namespacedEvent.split(".")[0] === "update") scope_Handles.forEach((function(a, index) {
                fireEvent("update", index);
            }));
        }
        function isInternalNamespace(namespace) {
            return namespace === INTERNAL_EVENT_NS.aria || namespace === INTERNAL_EVENT_NS.tooltips;
        }
        function removeEvent(namespacedEvent) {
            var event = namespacedEvent && namespacedEvent.split(".")[0];
            var namespace = event ? namespacedEvent.substring(event.length) : namespacedEvent;
            Object.keys(scope_Events).forEach((function(bind) {
                var tEvent = bind.split(".")[0];
                var tNamespace = bind.substring(tEvent.length);
                if ((!event || event === tEvent) && (!namespace || namespace === tNamespace)) if (!isInternalNamespace(tNamespace) || namespace === tNamespace) delete scope_Events[bind];
            }));
        }
        function fireEvent(eventName, handleNumber, tap) {
            Object.keys(scope_Events).forEach((function(targetEvent) {
                var eventType = targetEvent.split(".")[0];
                if (eventName === eventType) scope_Events[targetEvent].forEach((function(callback) {
                    callback.call(scope_Self, scope_Values.map(options.format.to), handleNumber, scope_Values.slice(), tap || false, scope_Locations.slice(), scope_Self);
                }));
            }));
        }
        function checkHandlePosition(reference, handleNumber, to, lookBackward, lookForward, getValue, smoothSteps) {
            var distance;
            if (scope_Handles.length > 1 && !options.events.unconstrained) {
                if (lookBackward && handleNumber > 0) {
                    distance = scope_Spectrum.getAbsoluteDistance(reference[handleNumber - 1], options.margin, false);
                    to = Math.max(to, distance);
                }
                if (lookForward && handleNumber < scope_Handles.length - 1) {
                    distance = scope_Spectrum.getAbsoluteDistance(reference[handleNumber + 1], options.margin, true);
                    to = Math.min(to, distance);
                }
            }
            if (scope_Handles.length > 1 && options.limit) {
                if (lookBackward && handleNumber > 0) {
                    distance = scope_Spectrum.getAbsoluteDistance(reference[handleNumber - 1], options.limit, false);
                    to = Math.min(to, distance);
                }
                if (lookForward && handleNumber < scope_Handles.length - 1) {
                    distance = scope_Spectrum.getAbsoluteDistance(reference[handleNumber + 1], options.limit, true);
                    to = Math.max(to, distance);
                }
            }
            if (options.padding) {
                if (handleNumber === 0) {
                    distance = scope_Spectrum.getAbsoluteDistance(0, options.padding[0], false);
                    to = Math.max(to, distance);
                }
                if (handleNumber === scope_Handles.length - 1) {
                    distance = scope_Spectrum.getAbsoluteDistance(100, options.padding[1], true);
                    to = Math.min(to, distance);
                }
            }
            if (!smoothSteps) to = scope_Spectrum.getStep(to);
            to = limit(to);
            if (to === reference[handleNumber] && !getValue) return false;
            return to;
        }
        function inRuleOrder(v, a) {
            var o = options.ort;
            return (o ? a : v) + ", " + (o ? v : a);
        }
        function moveHandles(upward, proposal, locations, handleNumbers, connect) {
            var proposals = locations.slice();
            var firstHandle = handleNumbers[0];
            var smoothSteps = options.events.smoothSteps;
            var b = [ !upward, upward ];
            var f = [ upward, !upward ];
            handleNumbers = handleNumbers.slice();
            if (upward) handleNumbers.reverse();
            if (handleNumbers.length > 1) handleNumbers.forEach((function(handleNumber, o) {
                var to = checkHandlePosition(proposals, handleNumber, proposals[handleNumber] + proposal, b[o], f[o], false, smoothSteps);
                if (to === false) proposal = 0; else {
                    proposal = to - proposals[handleNumber];
                    proposals[handleNumber] = to;
                }
            })); else b = f = [ true ];
            var state = false;
            handleNumbers.forEach((function(handleNumber, o) {
                state = setHandle(handleNumber, locations[handleNumber] + proposal, b[o], f[o], false, smoothSteps) || state;
            }));
            if (state) {
                handleNumbers.forEach((function(handleNumber) {
                    fireEvent("update", handleNumber);
                    fireEvent("slide", handleNumber);
                }));
                if (connect != void 0) fireEvent("drag", firstHandle);
            }
        }
        function transformDirection(a, b) {
            return options.dir ? 100 - a - b : a;
        }
        function updateHandlePosition(handleNumber, to) {
            scope_Locations[handleNumber] = to;
            scope_Values[handleNumber] = scope_Spectrum.fromStepping(to);
            var translation = transformDirection(to, 0) - scope_DirOffset;
            var translateRule = "translate(" + inRuleOrder(translation + "%", "0") + ")";
            scope_Handles[handleNumber].style[options.transformRule] = translateRule;
            if (options.events.invertConnects && scope_Locations.length > 1) {
                var handlesAreInOrder = scope_Locations.every((function(position, index, locations) {
                    return index === 0 || position >= locations[index - 1];
                }));
                if (scope_ConnectsInverted !== !handlesAreInOrder) {
                    invertConnects();
                    return;
                }
            }
            updateConnect(handleNumber);
            updateConnect(handleNumber + 1);
            if (scope_ConnectsInverted) {
                updateConnect(handleNumber - 1);
                updateConnect(handleNumber + 2);
            }
        }
        function setZindex() {
            scope_HandleNumbers.forEach((function(handleNumber) {
                var dir = scope_Locations[handleNumber] > 50 ? -1 : 1;
                var zIndex = 3 + (scope_Handles.length + dir * handleNumber);
                scope_Handles[handleNumber].style.zIndex = String(zIndex);
            }));
        }
        function setHandle(handleNumber, to, lookBackward, lookForward, exactInput, smoothSteps) {
            if (!exactInput) to = checkHandlePosition(scope_Locations, handleNumber, to, lookBackward, lookForward, false, smoothSteps);
            if (to === false) return false;
            updateHandlePosition(handleNumber, to);
            return true;
        }
        function updateConnect(index) {
            if (!scope_Connects[index]) return;
            var locations = scope_Locations.slice();
            if (scope_ConnectsInverted) locations.sort((function(a, b) {
                return a - b;
            }));
            var l = 0;
            var h = 100;
            if (index !== 0) l = locations[index - 1];
            if (index !== scope_Connects.length - 1) h = locations[index];
            var connectWidth = h - l;
            var translateRule = "translate(" + inRuleOrder(transformDirection(l, connectWidth) + "%", "0") + ")";
            var scaleRule = "scale(" + inRuleOrder(connectWidth / 100, "1") + ")";
            scope_Connects[index].style[options.transformRule] = translateRule + " " + scaleRule;
        }
        function resolveToValue(to, handleNumber) {
            if (to === null || to === false || to === void 0) return scope_Locations[handleNumber];
            if (typeof to === "number") to = String(to);
            to = options.format.from(to);
            if (to !== false) to = scope_Spectrum.toStepping(to);
            if (to === false || isNaN(to)) return scope_Locations[handleNumber];
            return to;
        }
        function valueSet(input, fireSetEvent, exactInput) {
            var values = asArray(input);
            var isInit = scope_Locations[0] === void 0;
            fireSetEvent = fireSetEvent === void 0 ? true : fireSetEvent;
            if (options.animate && !isInit) addClassFor(scope_Target, options.cssClasses.tap, options.animationDuration);
            scope_HandleNumbers.forEach((function(handleNumber) {
                setHandle(handleNumber, resolveToValue(values[handleNumber], handleNumber), true, false, exactInput);
            }));
            var i = scope_HandleNumbers.length === 1 ? 0 : 1;
            if (isInit && scope_Spectrum.hasNoSize()) {
                exactInput = true;
                scope_Locations[0] = 0;
                if (scope_HandleNumbers.length > 1) {
                    var space_1 = 100 / (scope_HandleNumbers.length - 1);
                    scope_HandleNumbers.forEach((function(handleNumber) {
                        scope_Locations[handleNumber] = handleNumber * space_1;
                    }));
                }
            }
            for (;i < scope_HandleNumbers.length; ++i) scope_HandleNumbers.forEach((function(handleNumber) {
                setHandle(handleNumber, scope_Locations[handleNumber], true, true, exactInput);
            }));
            setZindex();
            scope_HandleNumbers.forEach((function(handleNumber) {
                fireEvent("update", handleNumber);
                if (values[handleNumber] !== null && fireSetEvent) fireEvent("set", handleNumber);
            }));
        }
        function valueReset(fireSetEvent) {
            valueSet(options.start, fireSetEvent);
        }
        function valueSetHandle(handleNumber, value, fireSetEvent, exactInput) {
            handleNumber = Number(handleNumber);
            if (!(handleNumber >= 0 && handleNumber < scope_HandleNumbers.length)) throw new Error("noUiSlider: invalid handle number, got: " + handleNumber);
            setHandle(handleNumber, resolveToValue(value, handleNumber), true, true, exactInput);
            fireEvent("update", handleNumber);
            if (fireSetEvent) fireEvent("set", handleNumber);
        }
        function valueGet(unencoded) {
            if (unencoded === void 0) unencoded = false;
            if (unencoded) return scope_Values.length === 1 ? scope_Values[0] : scope_Values.slice(0);
            var values = scope_Values.map(options.format.to);
            if (values.length === 1) return values[0];
            return values;
        }
        function destroy() {
            removeEvent(INTERNAL_EVENT_NS.aria);
            removeEvent(INTERNAL_EVENT_NS.tooltips);
            Object.keys(options.cssClasses).forEach((function(key) {
                removeClass(scope_Target, options.cssClasses[key]);
            }));
            while (scope_Target.firstChild) scope_Target.removeChild(scope_Target.firstChild);
            delete scope_Target.noUiSlider;
        }
        function getNextStepsForHandle(handleNumber) {
            var location = scope_Locations[handleNumber];
            var nearbySteps = scope_Spectrum.getNearbySteps(location);
            var value = scope_Values[handleNumber];
            var increment = nearbySteps.thisStep.step;
            var decrement = null;
            if (options.snap) return [ value - nearbySteps.stepBefore.startValue || null, nearbySteps.stepAfter.startValue - value || null ];
            if (increment !== false) if (value + increment > nearbySteps.stepAfter.startValue) increment = nearbySteps.stepAfter.startValue - value;
            if (value > nearbySteps.thisStep.startValue) decrement = nearbySteps.thisStep.step; else if (nearbySteps.stepBefore.step === false) decrement = false; else decrement = value - nearbySteps.stepBefore.highestStep;
            if (location === 100) increment = null; else if (location === 0) decrement = null;
            var stepDecimals = scope_Spectrum.countStepDecimals();
            if (increment !== null && increment !== false) increment = Number(increment.toFixed(stepDecimals));
            if (decrement !== null && decrement !== false) decrement = Number(decrement.toFixed(stepDecimals));
            return [ decrement, increment ];
        }
        function getNextSteps() {
            return scope_HandleNumbers.map(getNextStepsForHandle);
        }
        function updateOptions(optionsToUpdate, fireSetEvent) {
            var v = valueGet();
            var updateAble = [ "margin", "limit", "padding", "range", "animate", "snap", "step", "format", "pips", "tooltips", "connect" ];
            updateAble.forEach((function(name) {
                if (optionsToUpdate[name] !== void 0) originalOptions[name] = optionsToUpdate[name];
            }));
            var newOptions = testOptions(originalOptions);
            updateAble.forEach((function(name) {
                if (optionsToUpdate[name] !== void 0) options[name] = newOptions[name];
            }));
            scope_Spectrum = newOptions.spectrum;
            options.margin = newOptions.margin;
            options.limit = newOptions.limit;
            options.padding = newOptions.padding;
            if (options.pips) pips(options.pips); else removePips();
            if (options.tooltips) tooltips(); else removeTooltips();
            scope_Locations = [];
            valueSet(isSet(optionsToUpdate.start) ? optionsToUpdate.start : v, fireSetEvent);
            if (optionsToUpdate.connect) updateConnectOption();
        }
        function updateConnectOption() {
            while (scope_ConnectBase.firstChild) scope_ConnectBase.removeChild(scope_ConnectBase.firstChild);
            for (var i = 0; i <= options.handles; i++) {
                scope_Connects[i] = addConnect(scope_ConnectBase, options.connect[i]);
                updateConnect(i);
            }
            bindSliderEvents({
                drag: options.events.drag,
                fixed: true
            });
        }
        function invertConnects() {
            scope_ConnectsInverted = !scope_ConnectsInverted;
            testConnect(options, options.connect.map((function(b) {
                return !b;
            })));
            updateConnectOption();
        }
        function setupSlider() {
            scope_Base = addSlider(scope_Target);
            addElements(options.connect, scope_Base);
            bindSliderEvents(options.events);
            valueSet(options.start);
            if (options.pips) pips(options.pips);
            if (options.tooltips) tooltips();
            aria();
        }
        setupSlider();
        var scope_Self = {
            destroy,
            steps: getNextSteps,
            on: bindEvent,
            off: removeEvent,
            get: valueGet,
            set: valueSet,
            setHandle: valueSetHandle,
            reset: valueReset,
            disable,
            enable,
            __moveHandles: function(upward, proposal, handleNumbers) {
                moveHandles(upward, proposal, scope_Locations, handleNumbers);
            },
            options: originalOptions,
            updateOptions,
            target: scope_Target,
            removePips,
            removeTooltips,
            getPositions: function() {
                return scope_Locations.slice();
            },
            getTooltips: function() {
                return scope_Tooltips;
            },
            getOrigins: function() {
                return scope_Handles;
            },
            pips
        };
        return scope_Self;
    }
    function initialize(target, originalOptions) {
        if (!target || !target.nodeName) throw new Error("noUiSlider: create requires a single element, got: " + target);
        if (target.noUiSlider) throw new Error("noUiSlider: Slider was already initialized.");
        var options = testOptions(originalOptions);
        var api = scope(target, options, originalOptions);
        target.noUiSlider = api;
        return api;
    }
    function rangeInit() {
        const sliders = document.querySelectorAll("[data-range-wrapper]");
        sliders.forEach((slider => {
            const priceSlider = slider.querySelector("[data-range]");
            if (priceSlider) {
                var textFrom = +priceSlider.getAttribute("data-from");
                var textTo = +priceSlider.getAttribute("data-to");
                let formatOptions = {};
                if (slider.dataset.rangeWrapper === "price") formatOptions = {
                    decimals: 0,
                    suffix: " ₴",
                    thousand: " "
                }; else if (slider.dataset.rangeWrapper === "power") formatOptions = {
                    decimals: 2,
                    suffix: " кВт"
                };
                initialize(priceSlider, {
                    start: [ textFrom, textTo ],
                    connect: true,
                    range: {
                        min: [ textFrom ],
                        max: [ textTo ]
                    },
                    format: wNumb(formatOptions)
                });
                const priceStart = slider.querySelector("[data-price-start]");
                const priceEnd = slider.querySelector("[data-price-end]");
                priceStart.addEventListener("change", (() => setPriceValues(priceSlider, priceStart, priceEnd)));
                priceEnd.addEventListener("change", (() => setPriceValues(priceSlider, priceStart, priceEnd)));
                priceSlider.noUiSlider.on("update", (function(values, handle) {
                    priceStart.value = `${values[0]}`;
                    priceEnd.value = `${values[1]}`;
                }));
            }
        }));
        function setPriceValues(priceSlider, priceStart, priceEnd) {
            let priceStartValue = priceStart.value !== "" ? priceStart.value : null;
            let priceEndValue = priceEnd.value !== "" ? priceEnd.value : null;
            priceSlider.noUiSlider.set([ priceStartValue, priceEndValue ]);
        }
    }
    rangeInit();
    document.addEventListener("DOMContentLoaded", (function() {
        const heroSectionSliders = document.querySelectorAll(".hero__slider");
        if (heroSectionSliders) heroSectionSliders.forEach((slider => {
            new Splide(slider, {
                perPage: 2,
                perMove: 1,
                gap: "1.5rem",
                arrows: false,
                pagination: true,
                focus: 0,
                speed: 800,
                autoplay: true,
                interval: 5e3,
                rewind: true,
                padding: {
                    right: "3rem"
                },
                breakpoints: {
                    991.98: {
                        perPage: 1,
                        padding: {
                            right: "3.375rem"
                        }
                    },
                    767.98: {
                        perPage: 1,
                        gap: "0.875rem"
                    }
                }
            }).mount();
        }));
        const brandSliders = document.querySelectorAll(".brands-section__slider");
        if (brandSliders) brandSliders.forEach((slider => {
            new Splide(slider, {
                type: "loop",
                perPage: 7,
                perMove: 1,
                arrows: true,
                autoplay: true,
                interval: 5e3,
                pauseOnHover: true,
                pagination: false,
                gap: "2.125rem",
                breakpoints: {
                    1299.98: {
                        perPage: 6
                    },
                    991.98: {
                        perPage: 5
                    },
                    767.98: {
                        perPage: 4,
                        gap: "1.5rem"
                    },
                    449.98: {
                        perPage: 3,
                        gap: "0.5rem",
                        padding: {
                            right: "2.375rem"
                        }
                    }
                }
            }).mount();
        }));
        const productsSectionSliders = document.querySelectorAll(".products-section__slider");
        if (productsSectionSliders) productsSectionSliders.forEach((slider => {
            new Splide(slider, {
                perPage: 4,
                perMove: 1,
                gap: "1.25rem",
                arrows: false,
                pagination: false,
                padding: {
                    right: "3.75rem"
                },
                breakpoints: {
                    1399.98: {
                        perPage: 3
                    },
                    1069.98: {
                        perPage: 2,
                        gap: "1rem"
                    },
                    739.98: {
                        gap: "0.625rem",
                        padding: {
                            right: "3.3125rem"
                        }
                    },
                    549.98: {
                        perPage: 1,
                        gap: "0.625rem",
                        padding: {
                            right: "2.875rem"
                        }
                    }
                }
            }).mount();
        }));
        const productsNewsSliders = document.querySelectorAll(".news-article__products-slider");
        if (productsNewsSliders) productsNewsSliders.forEach((slider => {
            new Splide(slider, {
                perPage: 4,
                perMove: 1,
                gap: "1.5rem",
                arrows: false,
                pagination: false,
                breakpoints: {
                    1399.98: {
                        perPage: 3
                    },
                    739.98: {
                        gap: "0.5rem"
                    },
                    649.98: {
                        perPage: 2,
                        padding: {
                            right: "1.8125rem"
                        }
                    }
                }
            }).mount();
        }));
        // const itemProductSliders = document.querySelectorAll(".item-product__slider");
        // if (itemProductSliders) itemProductSliders.forEach((slider => {
        //     new Splide(slider, {
        //         perPage: 1,
        //         arrows: true,
        //         pagination: true,
        //         drag: false
        //     }).mount();
        // }));
        const checkoutSliders = document.querySelectorAll(".block-aside-ch__slider");
        if (checkoutSliders) checkoutSliders.forEach((slider => {
            new Splide(slider, {
                perPage: 2,
                arrows: false,
                pagination: false,
                gap: "1.5rem",
                padding: {
                    right: "4rem"
                },
                breakpoints: {
                    767.98: {
                        gap: "0.5rem",
                        padding: {
                            right: "1.25rem"
                        }
                    },
                    389.98: {
                        perPage: 1
                    }
                }
            }).mount();
        }));
        const newsSectionSliders = document.querySelectorAll(".news-section__slider");
        if (newsSectionSliders) newsSectionSliders.forEach((slider => {
            new Splide(slider, {
                perPage: 2,
                perMove: 1,
                gap: "1.5rem",
                arrows: false,
                destroy: true,
                pagination: false,
                padding: {
                    right: "3.75rem"
                },
                breakpoints: {
                    991.98: {
                        destroy: false
                    },
                    739.98: {
                        perPage: 1,
                        gap: "0.875rem",
                        padding: {
                            right: "6.875rem"
                        }
                    },
                    389.98: {
                        perPage: 1,
                        padding: {
                            right: "2.5rem"
                        }
                    }
                }
            }).mount();
        }));
        const productSliders = document.querySelectorAll(".main-product__sliders");
        if (productSliders.length) productSliders.forEach((sliderWrapper => {
            const mainSlider = sliderWrapper.querySelector(".main-product__main-slider");
            const thumbnailSlider = sliderWrapper.querySelector(".main-product__thumbnail-slider");
            if (mainSlider && thumbnailSlider) {
                const mainSplide = new Splide(mainSlider, {
                    type: "fade",
                    perPage: 1,
                    pagination: true,
                    arrows: true
                }).mount();
                const thumbSplide = new Splide(thumbnailSlider, {
                    isNavigation: true,
                    perPage: 4,
                    gap: 10,
                    pagination: false,
                    arrows: false,
                    breakpoints: {
                        991.98: {
                            perPage: 3
                        }
                    }
                }).mount();
                mainSplide.sync(thumbSplide);
            }
        }));
    }));
    function isObject(value) {
        var type = typeof value;
        return value != null && (type == "object" || type == "function");
    }
    const lodash_es_isObject = isObject;
    var freeGlobal = typeof global == "object" && global && global.Object === Object && global;
    const _freeGlobal = freeGlobal;
    var freeSelf = typeof self == "object" && self && self.Object === Object && self;
    var root = _freeGlobal || freeSelf || Function("return this")();
    const _root = root;
    var now = function() {
        return _root.Date.now();
    };
    const lodash_es_now = now;
    var reWhitespace = /\s/;
    function trimmedEndIndex(string) {
        var index = string.length;
        while (index-- && reWhitespace.test(string.charAt(index))) ;
        return index;
    }
    const _trimmedEndIndex = trimmedEndIndex;
    var reTrimStart = /^\s+/;
    function baseTrim(string) {
        return string ? string.slice(0, _trimmedEndIndex(string) + 1).replace(reTrimStart, "") : string;
    }
    const _baseTrim = baseTrim;
    var Symbol = _root.Symbol;
    const _Symbol = Symbol;
    var objectProto = Object.prototype;
    var _getRawTag_hasOwnProperty = objectProto.hasOwnProperty;
    var nativeObjectToString = objectProto.toString;
    var symToStringTag = _Symbol ? _Symbol.toStringTag : void 0;
    function getRawTag(value) {
        var isOwn = _getRawTag_hasOwnProperty.call(value, symToStringTag), tag = value[symToStringTag];
        try {
            value[symToStringTag] = void 0;
            var unmasked = true;
        } catch (e) {}
        var result = nativeObjectToString.call(value);
        if (unmasked) if (isOwn) value[symToStringTag] = tag; else delete value[symToStringTag];
        return result;
    }
    const _getRawTag = getRawTag;
    var _objectToString_objectProto = Object.prototype;
    var _objectToString_nativeObjectToString = _objectToString_objectProto.toString;
    function objectToString(value) {
        return _objectToString_nativeObjectToString.call(value);
    }
    const _objectToString = objectToString;
    var nullTag = "[object Null]", undefinedTag = "[object Undefined]";
    var _baseGetTag_symToStringTag = _Symbol ? _Symbol.toStringTag : void 0;
    function baseGetTag(value) {
        if (value == null) return value === void 0 ? undefinedTag : nullTag;
        return _baseGetTag_symToStringTag && _baseGetTag_symToStringTag in Object(value) ? _getRawTag(value) : _objectToString(value);
    }
    const _baseGetTag = baseGetTag;
    function isObjectLike(value) {
        return value != null && typeof value == "object";
    }
    const lodash_es_isObjectLike = isObjectLike;
    var symbolTag = "[object Symbol]";
    function isSymbol(value) {
        return typeof value == "symbol" || lodash_es_isObjectLike(value) && _baseGetTag(value) == symbolTag;
    }
    const lodash_es_isSymbol = isSymbol;
    var NAN = 0 / 0;
    var reIsBadHex = /^[-+]0x[0-9a-f]+$/i;
    var reIsBinary = /^0b[01]+$/i;
    var reIsOctal = /^0o[0-7]+$/i;
    var freeParseInt = parseInt;
    function toNumber(value) {
        if (typeof value == "number") return value;
        if (lodash_es_isSymbol(value)) return NAN;
        if (lodash_es_isObject(value)) {
            var other = typeof value.valueOf == "function" ? value.valueOf() : value;
            value = lodash_es_isObject(other) ? other + "" : other;
        }
        if (typeof value != "string") return value === 0 ? value : +value;
        value = _baseTrim(value);
        var isBinary = reIsBinary.test(value);
        return isBinary || reIsOctal.test(value) ? freeParseInt(value.slice(2), isBinary ? 2 : 8) : reIsBadHex.test(value) ? NAN : +value;
    }
    const lodash_es_toNumber = toNumber;
    var FUNC_ERROR_TEXT = "Expected a function";
    var nativeMax = Math.max, nativeMin = Math.min;
    function debounce(func, wait, options) {
        var lastArgs, lastThis, maxWait, result, timerId, lastCallTime, lastInvokeTime = 0, leading = false, maxing = false, trailing = true;
        if (typeof func != "function") throw new TypeError(FUNC_ERROR_TEXT);
        wait = lodash_es_toNumber(wait) || 0;
        if (lodash_es_isObject(options)) {
            leading = !!options.leading;
            maxing = "maxWait" in options;
            maxWait = maxing ? nativeMax(lodash_es_toNumber(options.maxWait) || 0, wait) : maxWait;
            trailing = "trailing" in options ? !!options.trailing : trailing;
        }
        function invokeFunc(time) {
            var args = lastArgs, thisArg = lastThis;
            lastArgs = lastThis = void 0;
            lastInvokeTime = time;
            result = func.apply(thisArg, args);
            return result;
        }
        function leadingEdge(time) {
            lastInvokeTime = time;
            timerId = setTimeout(timerExpired, wait);
            return leading ? invokeFunc(time) : result;
        }
        function remainingWait(time) {
            var timeSinceLastCall = time - lastCallTime, timeSinceLastInvoke = time - lastInvokeTime, timeWaiting = wait - timeSinceLastCall;
            return maxing ? nativeMin(timeWaiting, maxWait - timeSinceLastInvoke) : timeWaiting;
        }
        function shouldInvoke(time) {
            var timeSinceLastCall = time - lastCallTime, timeSinceLastInvoke = time - lastInvokeTime;
            return lastCallTime === void 0 || timeSinceLastCall >= wait || timeSinceLastCall < 0 || maxing && timeSinceLastInvoke >= maxWait;
        }
        function timerExpired() {
            var time = lodash_es_now();
            if (shouldInvoke(time)) return trailingEdge(time);
            timerId = setTimeout(timerExpired, remainingWait(time));
        }
        function trailingEdge(time) {
            timerId = void 0;
            if (trailing && lastArgs) return invokeFunc(time);
            lastArgs = lastThis = void 0;
            return result;
        }
        function cancel() {
            if (timerId !== void 0) clearTimeout(timerId);
            lastInvokeTime = 0;
            lastArgs = lastCallTime = lastThis = timerId = void 0;
        }
        function flush() {
            return timerId === void 0 ? result : trailingEdge(lodash_es_now());
        }
        function debounced() {
            var time = lodash_es_now(), isInvoking = shouldInvoke(time);
            lastArgs = arguments;
            lastThis = this;
            lastCallTime = time;
            if (isInvoking) {
                if (timerId === void 0) return leadingEdge(lastCallTime);
                if (maxing) {
                    clearTimeout(timerId);
                    timerId = setTimeout(timerExpired, wait);
                    return invokeFunc(lastCallTime);
                }
            }
            if (timerId === void 0) timerId = setTimeout(timerExpired, wait);
            return result;
        }
        debounced.cancel = cancel;
        debounced.flush = flush;
        return debounced;
    }
    const lodash_es_debounce = debounce;
    var throttle_FUNC_ERROR_TEXT = "Expected a function";
    function throttle(func, wait, options) {
        var leading = true, trailing = true;
        if (typeof func != "function") throw new TypeError(throttle_FUNC_ERROR_TEXT);
        if (lodash_es_isObject(options)) {
            leading = "leading" in options ? !!options.leading : leading;
            trailing = "trailing" in options ? !!options.trailing : trailing;
        }
        return lodash_es_debounce(func, wait, {
            leading,
            maxWait: wait,
            trailing
        });
    }
    const lodash_es_throttle = throttle;
    var __assign = function() {
        __assign = Object.assign || function __assign(t) {
            for (var s, i = 1, n = arguments.length; i < n; i++) {
                s = arguments[i];
                for (var p in s) if (Object.prototype.hasOwnProperty.call(s, p)) t[p] = s[p];
            }
            return t;
        };
        return __assign.apply(this, arguments);
    };
    function getElementWindow$1(element) {
        if (!element || !element.ownerDocument || !element.ownerDocument.defaultView) return window;
        return element.ownerDocument.defaultView;
    }
    function getElementDocument$1(element) {
        if (!element || !element.ownerDocument) return document;
        return element.ownerDocument;
    }
    var getOptions$1 = function(obj) {
        var initialObj = {};
        var options = Array.prototype.reduce.call(obj, (function(acc, attribute) {
            var option = attribute.name.match(/data-simplebar-(.+)/);
            if (option) {
                var key = option[1].replace(/\W+(.)/g, (function(_, chr) {
                    return chr.toUpperCase();
                }));
                switch (attribute.value) {
                  case "true":
                    acc[key] = true;
                    break;

                  case "false":
                    acc[key] = false;
                    break;

                  case void 0:
                    acc[key] = true;
                    break;

                  default:
                    acc[key] = attribute.value;
                }
            }
            return acc;
        }), initialObj);
        return options;
    };
    function addClasses$1(el, classes) {
        var _a;
        if (!el) return;
        (_a = el.classList).add.apply(_a, classes.split(" "));
    }
    function removeClasses$1(el, classes) {
        if (!el) return;
        classes.split(" ").forEach((function(className) {
            el.classList.remove(className);
        }));
    }
    function classNamesToQuery$1(classNames) {
        return ".".concat(classNames.split(" ").join("."));
    }
    var canUseDOM = !!(typeof window !== "undefined" && window.document && window.document.createElement);
    var helpers = Object.freeze({
        __proto__: null,
        addClasses: addClasses$1,
        canUseDOM,
        classNamesToQuery: classNamesToQuery$1,
        getElementDocument: getElementDocument$1,
        getElementWindow: getElementWindow$1,
        getOptions: getOptions$1,
        removeClasses: removeClasses$1
    });
    var cachedScrollbarWidth = null;
    var cachedDevicePixelRatio = null;
    if (canUseDOM) window.addEventListener("resize", (function() {
        if (cachedDevicePixelRatio !== window.devicePixelRatio) {
            cachedDevicePixelRatio = window.devicePixelRatio;
            cachedScrollbarWidth = null;
        }
    }));
    function scrollbarWidth() {
        if (cachedScrollbarWidth === null) {
            if (typeof document === "undefined") {
                cachedScrollbarWidth = 0;
                return cachedScrollbarWidth;
            }
            var body = document.body;
            var box = document.createElement("div");
            box.classList.add("simplebar-hide-scrollbar");
            body.appendChild(box);
            var width = box.getBoundingClientRect().right;
            body.removeChild(box);
            cachedScrollbarWidth = width;
        }
        return cachedScrollbarWidth;
    }
    var getElementWindow = getElementWindow$1, getElementDocument = getElementDocument$1, getOptions = getOptions$1, addClasses = addClasses$1, dist_removeClasses = removeClasses$1, classNamesToQuery = classNamesToQuery$1;
    var SimpleBarCore = function() {
        function SimpleBarCore(element, options) {
            if (options === void 0) options = {};
            var _this = this;
            this.removePreventClickId = null;
            this.minScrollbarWidth = 20;
            this.stopScrollDelay = 175;
            this.isScrolling = false;
            this.isMouseEntering = false;
            this.isDragging = false;
            this.scrollXTicking = false;
            this.scrollYTicking = false;
            this.wrapperEl = null;
            this.contentWrapperEl = null;
            this.contentEl = null;
            this.offsetEl = null;
            this.maskEl = null;
            this.placeholderEl = null;
            this.heightAutoObserverWrapperEl = null;
            this.heightAutoObserverEl = null;
            this.rtlHelpers = null;
            this.scrollbarWidth = 0;
            this.resizeObserver = null;
            this.mutationObserver = null;
            this.elStyles = null;
            this.isRtl = null;
            this.mouseX = 0;
            this.mouseY = 0;
            this.onMouseMove = function() {};
            this.onWindowResize = function() {};
            this.onStopScrolling = function() {};
            this.onMouseEntered = function() {};
            this.onScroll = function() {
                var elWindow = getElementWindow(_this.el);
                if (!_this.scrollXTicking) {
                    elWindow.requestAnimationFrame(_this.scrollX);
                    _this.scrollXTicking = true;
                }
                if (!_this.scrollYTicking) {
                    elWindow.requestAnimationFrame(_this.scrollY);
                    _this.scrollYTicking = true;
                }
                if (!_this.isScrolling) {
                    _this.isScrolling = true;
                    addClasses(_this.el, _this.classNames.scrolling);
                }
                _this.showScrollbar("x");
                _this.showScrollbar("y");
                _this.onStopScrolling();
            };
            this.scrollX = function() {
                if (_this.axis.x.isOverflowing) _this.positionScrollbar("x");
                _this.scrollXTicking = false;
            };
            this.scrollY = function() {
                if (_this.axis.y.isOverflowing) _this.positionScrollbar("y");
                _this.scrollYTicking = false;
            };
            this._onStopScrolling = function() {
                dist_removeClasses(_this.el, _this.classNames.scrolling);
                if (_this.options.autoHide) {
                    _this.hideScrollbar("x");
                    _this.hideScrollbar("y");
                }
                _this.isScrolling = false;
            };
            this.onMouseEnter = function() {
                if (!_this.isMouseEntering) {
                    addClasses(_this.el, _this.classNames.mouseEntered);
                    _this.showScrollbar("x");
                    _this.showScrollbar("y");
                    _this.isMouseEntering = true;
                }
                _this.onMouseEntered();
            };
            this._onMouseEntered = function() {
                dist_removeClasses(_this.el, _this.classNames.mouseEntered);
                if (_this.options.autoHide) {
                    _this.hideScrollbar("x");
                    _this.hideScrollbar("y");
                }
                _this.isMouseEntering = false;
            };
            this._onMouseMove = function(e) {
                _this.mouseX = e.clientX;
                _this.mouseY = e.clientY;
                if (_this.axis.x.isOverflowing || _this.axis.x.forceVisible) _this.onMouseMoveForAxis("x");
                if (_this.axis.y.isOverflowing || _this.axis.y.forceVisible) _this.onMouseMoveForAxis("y");
            };
            this.onMouseLeave = function() {
                _this.onMouseMove.cancel();
                if (_this.axis.x.isOverflowing || _this.axis.x.forceVisible) _this.onMouseLeaveForAxis("x");
                if (_this.axis.y.isOverflowing || _this.axis.y.forceVisible) _this.onMouseLeaveForAxis("y");
                _this.mouseX = -1;
                _this.mouseY = -1;
            };
            this._onWindowResize = function() {
                _this.scrollbarWidth = _this.getScrollbarWidth();
                _this.hideNativeScrollbar();
            };
            this.onPointerEvent = function(e) {
                if (!_this.axis.x.track.el || !_this.axis.y.track.el || !_this.axis.x.scrollbar.el || !_this.axis.y.scrollbar.el) return;
                var isWithinTrackXBounds, isWithinTrackYBounds;
                _this.axis.x.track.rect = _this.axis.x.track.el.getBoundingClientRect();
                _this.axis.y.track.rect = _this.axis.y.track.el.getBoundingClientRect();
                if (_this.axis.x.isOverflowing || _this.axis.x.forceVisible) isWithinTrackXBounds = _this.isWithinBounds(_this.axis.x.track.rect);
                if (_this.axis.y.isOverflowing || _this.axis.y.forceVisible) isWithinTrackYBounds = _this.isWithinBounds(_this.axis.y.track.rect);
                if (isWithinTrackXBounds || isWithinTrackYBounds) {
                    e.stopPropagation();
                    if (e.type === "pointerdown" && e.pointerType !== "touch") {
                        if (isWithinTrackXBounds) {
                            _this.axis.x.scrollbar.rect = _this.axis.x.scrollbar.el.getBoundingClientRect();
                            if (_this.isWithinBounds(_this.axis.x.scrollbar.rect)) _this.onDragStart(e, "x"); else _this.onTrackClick(e, "x");
                        }
                        if (isWithinTrackYBounds) {
                            _this.axis.y.scrollbar.rect = _this.axis.y.scrollbar.el.getBoundingClientRect();
                            if (_this.isWithinBounds(_this.axis.y.scrollbar.rect)) _this.onDragStart(e, "y"); else _this.onTrackClick(e, "y");
                        }
                    }
                }
            };
            this.drag = function(e) {
                var _a, _b, _c, _d, _e, _f, _g, _h, _j, _k, _l;
                if (!_this.draggedAxis || !_this.contentWrapperEl) return;
                var eventOffset;
                var track = _this.axis[_this.draggedAxis].track;
                var trackSize = (_b = (_a = track.rect) === null || _a === void 0 ? void 0 : _a[_this.axis[_this.draggedAxis].sizeAttr]) !== null && _b !== void 0 ? _b : 0;
                var scrollbar = _this.axis[_this.draggedAxis].scrollbar;
                var contentSize = (_d = (_c = _this.contentWrapperEl) === null || _c === void 0 ? void 0 : _c[_this.axis[_this.draggedAxis].scrollSizeAttr]) !== null && _d !== void 0 ? _d : 0;
                var hostSize = parseInt((_f = (_e = _this.elStyles) === null || _e === void 0 ? void 0 : _e[_this.axis[_this.draggedAxis].sizeAttr]) !== null && _f !== void 0 ? _f : "0px", 10);
                e.preventDefault();
                e.stopPropagation();
                if (_this.draggedAxis === "y") eventOffset = e.pageY; else eventOffset = e.pageX;
                var dragPos = eventOffset - ((_h = (_g = track.rect) === null || _g === void 0 ? void 0 : _g[_this.axis[_this.draggedAxis].offsetAttr]) !== null && _h !== void 0 ? _h : 0) - _this.axis[_this.draggedAxis].dragOffset;
                dragPos = _this.draggedAxis === "x" && _this.isRtl ? ((_k = (_j = track.rect) === null || _j === void 0 ? void 0 : _j[_this.axis[_this.draggedAxis].sizeAttr]) !== null && _k !== void 0 ? _k : 0) - scrollbar.size - dragPos : dragPos;
                var dragPerc = dragPos / (trackSize - scrollbar.size);
                var scrollPos = dragPerc * (contentSize - hostSize);
                if (_this.draggedAxis === "x" && _this.isRtl) scrollPos = ((_l = SimpleBarCore.getRtlHelpers()) === null || _l === void 0 ? void 0 : _l.isScrollingToNegative) ? -scrollPos : scrollPos;
                _this.contentWrapperEl[_this.axis[_this.draggedAxis].scrollOffsetAttr] = scrollPos;
            };
            this.onEndDrag = function(e) {
                _this.isDragging = false;
                var elDocument = getElementDocument(_this.el);
                var elWindow = getElementWindow(_this.el);
                e.preventDefault();
                e.stopPropagation();
                dist_removeClasses(_this.el, _this.classNames.dragging);
                _this.onStopScrolling();
                elDocument.removeEventListener("mousemove", _this.drag, true);
                elDocument.removeEventListener("mouseup", _this.onEndDrag, true);
                _this.removePreventClickId = elWindow.setTimeout((function() {
                    elDocument.removeEventListener("click", _this.preventClick, true);
                    elDocument.removeEventListener("dblclick", _this.preventClick, true);
                    _this.removePreventClickId = null;
                }));
            };
            this.preventClick = function(e) {
                e.preventDefault();
                e.stopPropagation();
            };
            this.el = element;
            this.options = __assign(__assign({}, SimpleBarCore.defaultOptions), options);
            this.classNames = __assign(__assign({}, SimpleBarCore.defaultOptions.classNames), options.classNames);
            this.axis = {
                x: {
                    scrollOffsetAttr: "scrollLeft",
                    sizeAttr: "width",
                    scrollSizeAttr: "scrollWidth",
                    offsetSizeAttr: "offsetWidth",
                    offsetAttr: "left",
                    overflowAttr: "overflowX",
                    dragOffset: 0,
                    isOverflowing: true,
                    forceVisible: false,
                    track: {
                        size: null,
                        el: null,
                        rect: null,
                        isVisible: false
                    },
                    scrollbar: {
                        size: null,
                        el: null,
                        rect: null,
                        isVisible: false
                    }
                },
                y: {
                    scrollOffsetAttr: "scrollTop",
                    sizeAttr: "height",
                    scrollSizeAttr: "scrollHeight",
                    offsetSizeAttr: "offsetHeight",
                    offsetAttr: "top",
                    overflowAttr: "overflowY",
                    dragOffset: 0,
                    isOverflowing: true,
                    forceVisible: false,
                    track: {
                        size: null,
                        el: null,
                        rect: null,
                        isVisible: false
                    },
                    scrollbar: {
                        size: null,
                        el: null,
                        rect: null,
                        isVisible: false
                    }
                }
            };
            if (typeof this.el !== "object" || !this.el.nodeName) throw new Error("Argument passed to SimpleBar must be an HTML element instead of ".concat(this.el));
            this.onMouseMove = lodash_es_throttle(this._onMouseMove, 64);
            this.onWindowResize = lodash_es_debounce(this._onWindowResize, 64, {
                leading: true
            });
            this.onStopScrolling = lodash_es_debounce(this._onStopScrolling, this.stopScrollDelay);
            this.onMouseEntered = lodash_es_debounce(this._onMouseEntered, this.stopScrollDelay);
            this.init();
        }
        SimpleBarCore.getRtlHelpers = function() {
            if (SimpleBarCore.rtlHelpers) return SimpleBarCore.rtlHelpers;
            var dummyDiv = document.createElement("div");
            dummyDiv.innerHTML = '<div class="simplebar-dummy-scrollbar-size"><div></div></div>';
            var scrollbarDummyEl = dummyDiv.firstElementChild;
            var dummyChild = scrollbarDummyEl === null || scrollbarDummyEl === void 0 ? void 0 : scrollbarDummyEl.firstElementChild;
            if (!dummyChild) return null;
            document.body.appendChild(scrollbarDummyEl);
            scrollbarDummyEl.scrollLeft = 0;
            var dummyContainerOffset = SimpleBarCore.getOffset(scrollbarDummyEl);
            var dummyChildOffset = SimpleBarCore.getOffset(dummyChild);
            scrollbarDummyEl.scrollLeft = -999;
            var dummyChildOffsetAfterScroll = SimpleBarCore.getOffset(dummyChild);
            document.body.removeChild(scrollbarDummyEl);
            SimpleBarCore.rtlHelpers = {
                isScrollOriginAtZero: dummyContainerOffset.left !== dummyChildOffset.left,
                isScrollingToNegative: dummyChildOffset.left !== dummyChildOffsetAfterScroll.left
            };
            return SimpleBarCore.rtlHelpers;
        };
        SimpleBarCore.prototype.getScrollbarWidth = function() {
            try {
                if (this.contentWrapperEl && getComputedStyle(this.contentWrapperEl, "::-webkit-scrollbar").display === "none" || "scrollbarWidth" in document.documentElement.style || "-ms-overflow-style" in document.documentElement.style) return 0; else return scrollbarWidth();
            } catch (e) {
                return scrollbarWidth();
            }
        };
        SimpleBarCore.getOffset = function(el) {
            var rect = el.getBoundingClientRect();
            var elDocument = getElementDocument(el);
            var elWindow = getElementWindow(el);
            return {
                top: rect.top + (elWindow.pageYOffset || elDocument.documentElement.scrollTop),
                left: rect.left + (elWindow.pageXOffset || elDocument.documentElement.scrollLeft)
            };
        };
        SimpleBarCore.prototype.init = function() {
            if (canUseDOM) {
                this.initDOM();
                this.rtlHelpers = SimpleBarCore.getRtlHelpers();
                this.scrollbarWidth = this.getScrollbarWidth();
                this.recalculate();
                this.initListeners();
            }
        };
        SimpleBarCore.prototype.initDOM = function() {
            var _a, _b;
            this.wrapperEl = this.el.querySelector(classNamesToQuery(this.classNames.wrapper));
            this.contentWrapperEl = this.options.scrollableNode || this.el.querySelector(classNamesToQuery(this.classNames.contentWrapper));
            this.contentEl = this.options.contentNode || this.el.querySelector(classNamesToQuery(this.classNames.contentEl));
            this.offsetEl = this.el.querySelector(classNamesToQuery(this.classNames.offset));
            this.maskEl = this.el.querySelector(classNamesToQuery(this.classNames.mask));
            this.placeholderEl = this.findChild(this.wrapperEl, classNamesToQuery(this.classNames.placeholder));
            this.heightAutoObserverWrapperEl = this.el.querySelector(classNamesToQuery(this.classNames.heightAutoObserverWrapperEl));
            this.heightAutoObserverEl = this.el.querySelector(classNamesToQuery(this.classNames.heightAutoObserverEl));
            this.axis.x.track.el = this.findChild(this.el, "".concat(classNamesToQuery(this.classNames.track)).concat(classNamesToQuery(this.classNames.horizontal)));
            this.axis.y.track.el = this.findChild(this.el, "".concat(classNamesToQuery(this.classNames.track)).concat(classNamesToQuery(this.classNames.vertical)));
            this.axis.x.scrollbar.el = ((_a = this.axis.x.track.el) === null || _a === void 0 ? void 0 : _a.querySelector(classNamesToQuery(this.classNames.scrollbar))) || null;
            this.axis.y.scrollbar.el = ((_b = this.axis.y.track.el) === null || _b === void 0 ? void 0 : _b.querySelector(classNamesToQuery(this.classNames.scrollbar))) || null;
            if (!this.options.autoHide) {
                addClasses(this.axis.x.scrollbar.el, this.classNames.visible);
                addClasses(this.axis.y.scrollbar.el, this.classNames.visible);
            }
        };
        SimpleBarCore.prototype.initListeners = function() {
            var _this = this;
            var _a;
            var elWindow = getElementWindow(this.el);
            this.el.addEventListener("mouseenter", this.onMouseEnter);
            this.el.addEventListener("pointerdown", this.onPointerEvent, true);
            this.el.addEventListener("mousemove", this.onMouseMove);
            this.el.addEventListener("mouseleave", this.onMouseLeave);
            (_a = this.contentWrapperEl) === null || _a === void 0 ? void 0 : _a.addEventListener("scroll", this.onScroll);
            elWindow.addEventListener("resize", this.onWindowResize);
            if (!this.contentEl) return;
            if (window.ResizeObserver) {
                var resizeObserverStarted_1 = false;
                var resizeObserver = elWindow.ResizeObserver || ResizeObserver;
                this.resizeObserver = new resizeObserver((function() {
                    if (!resizeObserverStarted_1) return;
                    elWindow.requestAnimationFrame((function() {
                        _this.recalculate();
                    }));
                }));
                this.resizeObserver.observe(this.el);
                this.resizeObserver.observe(this.contentEl);
                elWindow.requestAnimationFrame((function() {
                    resizeObserverStarted_1 = true;
                }));
            }
            this.mutationObserver = new elWindow.MutationObserver((function() {
                elWindow.requestAnimationFrame((function() {
                    _this.recalculate();
                }));
            }));
            this.mutationObserver.observe(this.contentEl, {
                childList: true,
                subtree: true,
                characterData: true
            });
        };
        SimpleBarCore.prototype.recalculate = function() {
            if (!this.heightAutoObserverEl || !this.contentEl || !this.contentWrapperEl || !this.wrapperEl || !this.placeholderEl) return;
            var elWindow = getElementWindow(this.el);
            this.elStyles = elWindow.getComputedStyle(this.el);
            this.isRtl = this.elStyles.direction === "rtl";
            var contentElOffsetWidth = this.contentEl.offsetWidth;
            var isHeightAuto = this.heightAutoObserverEl.offsetHeight <= 1;
            var isWidthAuto = this.heightAutoObserverEl.offsetWidth <= 1 || contentElOffsetWidth > 0;
            var contentWrapperElOffsetWidth = this.contentWrapperEl.offsetWidth;
            var elOverflowX = this.elStyles.overflowX;
            var elOverflowY = this.elStyles.overflowY;
            this.contentEl.style.padding = "".concat(this.elStyles.paddingTop, " ").concat(this.elStyles.paddingRight, " ").concat(this.elStyles.paddingBottom, " ").concat(this.elStyles.paddingLeft);
            this.wrapperEl.style.margin = "-".concat(this.elStyles.paddingTop, " -").concat(this.elStyles.paddingRight, " -").concat(this.elStyles.paddingBottom, " -").concat(this.elStyles.paddingLeft);
            var contentElScrollHeight = this.contentEl.scrollHeight;
            var contentElScrollWidth = this.contentEl.scrollWidth;
            this.contentWrapperEl.style.height = isHeightAuto ? "auto" : "100%";
            this.placeholderEl.style.width = isWidthAuto ? "".concat(contentElOffsetWidth || contentElScrollWidth, "px") : "auto";
            this.placeholderEl.style.height = "".concat(contentElScrollHeight, "px");
            var contentWrapperElOffsetHeight = this.contentWrapperEl.offsetHeight;
            this.axis.x.isOverflowing = contentElOffsetWidth !== 0 && contentElScrollWidth > contentElOffsetWidth;
            this.axis.y.isOverflowing = contentElScrollHeight > contentWrapperElOffsetHeight;
            this.axis.x.isOverflowing = elOverflowX === "hidden" ? false : this.axis.x.isOverflowing;
            this.axis.y.isOverflowing = elOverflowY === "hidden" ? false : this.axis.y.isOverflowing;
            this.axis.x.forceVisible = this.options.forceVisible === "x" || this.options.forceVisible === true;
            this.axis.y.forceVisible = this.options.forceVisible === "y" || this.options.forceVisible === true;
            this.hideNativeScrollbar();
            var offsetForXScrollbar = this.axis.x.isOverflowing ? this.scrollbarWidth : 0;
            var offsetForYScrollbar = this.axis.y.isOverflowing ? this.scrollbarWidth : 0;
            this.axis.x.isOverflowing = this.axis.x.isOverflowing && contentElScrollWidth > contentWrapperElOffsetWidth - offsetForYScrollbar;
            this.axis.y.isOverflowing = this.axis.y.isOverflowing && contentElScrollHeight > contentWrapperElOffsetHeight - offsetForXScrollbar;
            this.axis.x.scrollbar.size = this.getScrollbarSize("x");
            this.axis.y.scrollbar.size = this.getScrollbarSize("y");
            if (this.axis.x.scrollbar.el) this.axis.x.scrollbar.el.style.width = "".concat(this.axis.x.scrollbar.size, "px");
            if (this.axis.y.scrollbar.el) this.axis.y.scrollbar.el.style.height = "".concat(this.axis.y.scrollbar.size, "px");
            this.positionScrollbar("x");
            this.positionScrollbar("y");
            this.toggleTrackVisibility("x");
            this.toggleTrackVisibility("y");
        };
        SimpleBarCore.prototype.getScrollbarSize = function(axis) {
            var _a, _b;
            if (axis === void 0) axis = "y";
            if (!this.axis[axis].isOverflowing || !this.contentEl) return 0;
            var contentSize = this.contentEl[this.axis[axis].scrollSizeAttr];
            var trackSize = (_b = (_a = this.axis[axis].track.el) === null || _a === void 0 ? void 0 : _a[this.axis[axis].offsetSizeAttr]) !== null && _b !== void 0 ? _b : 0;
            var scrollbarRatio = trackSize / contentSize;
            var scrollbarSize;
            scrollbarSize = Math.max(~~(scrollbarRatio * trackSize), this.options.scrollbarMinSize);
            if (this.options.scrollbarMaxSize) scrollbarSize = Math.min(scrollbarSize, this.options.scrollbarMaxSize);
            return scrollbarSize;
        };
        SimpleBarCore.prototype.positionScrollbar = function(axis) {
            var _a, _b, _c;
            if (axis === void 0) axis = "y";
            var scrollbar = this.axis[axis].scrollbar;
            if (!this.axis[axis].isOverflowing || !this.contentWrapperEl || !scrollbar.el || !this.elStyles) return;
            var contentSize = this.contentWrapperEl[this.axis[axis].scrollSizeAttr];
            var trackSize = ((_a = this.axis[axis].track.el) === null || _a === void 0 ? void 0 : _a[this.axis[axis].offsetSizeAttr]) || 0;
            var hostSize = parseInt(this.elStyles[this.axis[axis].sizeAttr], 10);
            var scrollOffset = this.contentWrapperEl[this.axis[axis].scrollOffsetAttr];
            scrollOffset = axis === "x" && this.isRtl && ((_b = SimpleBarCore.getRtlHelpers()) === null || _b === void 0 ? void 0 : _b.isScrollOriginAtZero) ? -scrollOffset : scrollOffset;
            if (axis === "x" && this.isRtl) scrollOffset = ((_c = SimpleBarCore.getRtlHelpers()) === null || _c === void 0 ? void 0 : _c.isScrollingToNegative) ? scrollOffset : -scrollOffset;
            var scrollPourcent = scrollOffset / (contentSize - hostSize);
            var handleOffset = ~~((trackSize - scrollbar.size) * scrollPourcent);
            handleOffset = axis === "x" && this.isRtl ? -handleOffset + (trackSize - scrollbar.size) : handleOffset;
            scrollbar.el.style.transform = axis === "x" ? "translate3d(".concat(handleOffset, "px, 0, 0)") : "translate3d(0, ".concat(handleOffset, "px, 0)");
        };
        SimpleBarCore.prototype.toggleTrackVisibility = function(axis) {
            if (axis === void 0) axis = "y";
            var track = this.axis[axis].track.el;
            var scrollbar = this.axis[axis].scrollbar.el;
            if (!track || !scrollbar || !this.contentWrapperEl) return;
            if (this.axis[axis].isOverflowing || this.axis[axis].forceVisible) {
                track.style.visibility = "visible";
                this.contentWrapperEl.style[this.axis[axis].overflowAttr] = "scroll";
                this.el.classList.add("".concat(this.classNames.scrollable, "-").concat(axis));
            } else {
                track.style.visibility = "hidden";
                this.contentWrapperEl.style[this.axis[axis].overflowAttr] = "hidden";
                this.el.classList.remove("".concat(this.classNames.scrollable, "-").concat(axis));
            }
            if (this.axis[axis].isOverflowing) scrollbar.style.display = "block"; else scrollbar.style.display = "none";
        };
        SimpleBarCore.prototype.showScrollbar = function(axis) {
            if (axis === void 0) axis = "y";
            if (this.axis[axis].isOverflowing && !this.axis[axis].scrollbar.isVisible) {
                addClasses(this.axis[axis].scrollbar.el, this.classNames.visible);
                this.axis[axis].scrollbar.isVisible = true;
            }
        };
        SimpleBarCore.prototype.hideScrollbar = function(axis) {
            if (axis === void 0) axis = "y";
            if (this.isDragging) return;
            if (this.axis[axis].isOverflowing && this.axis[axis].scrollbar.isVisible) {
                dist_removeClasses(this.axis[axis].scrollbar.el, this.classNames.visible);
                this.axis[axis].scrollbar.isVisible = false;
            }
        };
        SimpleBarCore.prototype.hideNativeScrollbar = function() {
            if (!this.offsetEl) return;
            this.offsetEl.style[this.isRtl ? "left" : "right"] = this.axis.y.isOverflowing || this.axis.y.forceVisible ? "-".concat(this.scrollbarWidth, "px") : "0px";
            this.offsetEl.style.bottom = this.axis.x.isOverflowing || this.axis.x.forceVisible ? "-".concat(this.scrollbarWidth, "px") : "0px";
        };
        SimpleBarCore.prototype.onMouseMoveForAxis = function(axis) {
            if (axis === void 0) axis = "y";
            var currentAxis = this.axis[axis];
            if (!currentAxis.track.el || !currentAxis.scrollbar.el) return;
            currentAxis.track.rect = currentAxis.track.el.getBoundingClientRect();
            currentAxis.scrollbar.rect = currentAxis.scrollbar.el.getBoundingClientRect();
            if (this.isWithinBounds(currentAxis.track.rect)) {
                this.showScrollbar(axis);
                addClasses(currentAxis.track.el, this.classNames.hover);
                if (this.isWithinBounds(currentAxis.scrollbar.rect)) addClasses(currentAxis.scrollbar.el, this.classNames.hover); else dist_removeClasses(currentAxis.scrollbar.el, this.classNames.hover);
            } else {
                dist_removeClasses(currentAxis.track.el, this.classNames.hover);
                if (this.options.autoHide) this.hideScrollbar(axis);
            }
        };
        SimpleBarCore.prototype.onMouseLeaveForAxis = function(axis) {
            if (axis === void 0) axis = "y";
            dist_removeClasses(this.axis[axis].track.el, this.classNames.hover);
            dist_removeClasses(this.axis[axis].scrollbar.el, this.classNames.hover);
            if (this.options.autoHide) this.hideScrollbar(axis);
        };
        SimpleBarCore.prototype.onDragStart = function(e, axis) {
            var _a;
            if (axis === void 0) axis = "y";
            this.isDragging = true;
            var elDocument = getElementDocument(this.el);
            var elWindow = getElementWindow(this.el);
            var scrollbar = this.axis[axis].scrollbar;
            var eventOffset = axis === "y" ? e.pageY : e.pageX;
            this.axis[axis].dragOffset = eventOffset - (((_a = scrollbar.rect) === null || _a === void 0 ? void 0 : _a[this.axis[axis].offsetAttr]) || 0);
            this.draggedAxis = axis;
            addClasses(this.el, this.classNames.dragging);
            elDocument.addEventListener("mousemove", this.drag, true);
            elDocument.addEventListener("mouseup", this.onEndDrag, true);
            if (this.removePreventClickId === null) {
                elDocument.addEventListener("click", this.preventClick, true);
                elDocument.addEventListener("dblclick", this.preventClick, true);
            } else {
                elWindow.clearTimeout(this.removePreventClickId);
                this.removePreventClickId = null;
            }
        };
        SimpleBarCore.prototype.onTrackClick = function(e, axis) {
            var _this = this;
            var _a, _b, _c, _d;
            if (axis === void 0) axis = "y";
            var currentAxis = this.axis[axis];
            if (!this.options.clickOnTrack || !currentAxis.scrollbar.el || !this.contentWrapperEl) return;
            e.preventDefault();
            var elWindow = getElementWindow(this.el);
            this.axis[axis].scrollbar.rect = currentAxis.scrollbar.el.getBoundingClientRect();
            var scrollbar = this.axis[axis].scrollbar;
            var scrollbarOffset = (_b = (_a = scrollbar.rect) === null || _a === void 0 ? void 0 : _a[this.axis[axis].offsetAttr]) !== null && _b !== void 0 ? _b : 0;
            var hostSize = parseInt((_d = (_c = this.elStyles) === null || _c === void 0 ? void 0 : _c[this.axis[axis].sizeAttr]) !== null && _d !== void 0 ? _d : "0px", 10);
            var scrolled = this.contentWrapperEl[this.axis[axis].scrollOffsetAttr];
            var t = axis === "y" ? this.mouseY - scrollbarOffset : this.mouseX - scrollbarOffset;
            var dir = t < 0 ? -1 : 1;
            var scrollSize = dir === -1 ? scrolled - hostSize : scrolled + hostSize;
            var speed = 40;
            var scrollTo = function() {
                if (!_this.contentWrapperEl) return;
                if (dir === -1) {
                    if (scrolled > scrollSize) {
                        scrolled -= speed;
                        _this.contentWrapperEl[_this.axis[axis].scrollOffsetAttr] = scrolled;
                        elWindow.requestAnimationFrame(scrollTo);
                    }
                } else if (scrolled < scrollSize) {
                    scrolled += speed;
                    _this.contentWrapperEl[_this.axis[axis].scrollOffsetAttr] = scrolled;
                    elWindow.requestAnimationFrame(scrollTo);
                }
            };
            scrollTo();
        };
        SimpleBarCore.prototype.getContentElement = function() {
            return this.contentEl;
        };
        SimpleBarCore.prototype.getScrollElement = function() {
            return this.contentWrapperEl;
        };
        SimpleBarCore.prototype.removeListeners = function() {
            var elWindow = getElementWindow(this.el);
            this.el.removeEventListener("mouseenter", this.onMouseEnter);
            this.el.removeEventListener("pointerdown", this.onPointerEvent, true);
            this.el.removeEventListener("mousemove", this.onMouseMove);
            this.el.removeEventListener("mouseleave", this.onMouseLeave);
            if (this.contentWrapperEl) this.contentWrapperEl.removeEventListener("scroll", this.onScroll);
            elWindow.removeEventListener("resize", this.onWindowResize);
            if (this.mutationObserver) this.mutationObserver.disconnect();
            if (this.resizeObserver) this.resizeObserver.disconnect();
            this.onMouseMove.cancel();
            this.onWindowResize.cancel();
            this.onStopScrolling.cancel();
            this.onMouseEntered.cancel();
        };
        SimpleBarCore.prototype.unMount = function() {
            this.removeListeners();
        };
        SimpleBarCore.prototype.isWithinBounds = function(bbox) {
            return this.mouseX >= bbox.left && this.mouseX <= bbox.left + bbox.width && this.mouseY >= bbox.top && this.mouseY <= bbox.top + bbox.height;
        };
        SimpleBarCore.prototype.findChild = function(el, query) {
            var matches = el.matches || el.webkitMatchesSelector || el.mozMatchesSelector || el.msMatchesSelector;
            return Array.prototype.filter.call(el.children, (function(child) {
                return matches.call(child, query);
            }))[0];
        };
        SimpleBarCore.rtlHelpers = null;
        SimpleBarCore.defaultOptions = {
            forceVisible: false,
            clickOnTrack: true,
            scrollbarMinSize: 25,
            scrollbarMaxSize: 0,
            ariaLabel: "scrollable content",
            tabIndex: 0,
            classNames: {
                contentEl: "simplebar-content",
                contentWrapper: "simplebar-content-wrapper",
                offset: "simplebar-offset",
                mask: "simplebar-mask",
                wrapper: "simplebar-wrapper",
                placeholder: "simplebar-placeholder",
                scrollbar: "simplebar-scrollbar",
                track: "simplebar-track",
                heightAutoObserverWrapperEl: "simplebar-height-auto-observer-wrapper",
                heightAutoObserverEl: "simplebar-height-auto-observer",
                visible: "simplebar-visible",
                horizontal: "simplebar-horizontal",
                vertical: "simplebar-vertical",
                hover: "simplebar-hover",
                dragging: "simplebar-dragging",
                scrolling: "simplebar-scrolling",
                scrollable: "simplebar-scrollable",
                mouseEntered: "simplebar-mouse-entered"
            },
            scrollableNode: null,
            contentNode: null,
            autoHide: true
        };
        SimpleBarCore.getOptions = getOptions;
        SimpleBarCore.helpers = helpers;
        return SimpleBarCore;
    }();
    var extendStatics = function(d, b) {
        extendStatics = Object.setPrototypeOf || {
            __proto__: []
        } instanceof Array && function(d, b) {
            d.__proto__ = b;
        } || function(d, b) {
            for (var p in b) if (Object.prototype.hasOwnProperty.call(b, p)) d[p] = b[p];
        };
        return extendStatics(d, b);
    };
    function __extends(d, b) {
        if (typeof b !== "function" && b !== null) throw new TypeError("Class extends value " + String(b) + " is not a constructor or null");
        extendStatics(d, b);
        function __() {
            this.constructor = d;
        }
        d.prototype = b === null ? Object.create(b) : (__.prototype = b.prototype, new __);
    }
    var _a = SimpleBarCore.helpers, dist_getOptions = _a.getOptions, dist_addClasses = _a.addClasses, dist_canUseDOM = _a.canUseDOM;
    var SimpleBar = function(_super) {
        __extends(SimpleBar, _super);
        function SimpleBar() {
            var args = [];
            for (var _i = 0; _i < arguments.length; _i++) args[_i] = arguments[_i];
            var _this = _super.apply(this, args) || this;
            SimpleBar.instances.set(args[0], _this);
            return _this;
        }
        SimpleBar.initDOMLoadedElements = function() {
            document.removeEventListener("DOMContentLoaded", this.initDOMLoadedElements);
            window.removeEventListener("load", this.initDOMLoadedElements);
            Array.prototype.forEach.call(document.querySelectorAll("[data-simplebar]"), (function(el) {
                if (el.getAttribute("data-simplebar") !== "init" && !SimpleBar.instances.has(el)) new SimpleBar(el, dist_getOptions(el.attributes));
            }));
        };
        SimpleBar.removeObserver = function() {
            var _a;
            (_a = SimpleBar.globalObserver) === null || _a === void 0 ? void 0 : _a.disconnect();
        };
        SimpleBar.prototype.initDOM = function() {
            var _this = this;
            var _a, _b, _c;
            if (!Array.prototype.filter.call(this.el.children, (function(child) {
                return child.classList.contains(_this.classNames.wrapper);
            })).length) {
                this.wrapperEl = document.createElement("div");
                this.contentWrapperEl = document.createElement("div");
                this.offsetEl = document.createElement("div");
                this.maskEl = document.createElement("div");
                this.contentEl = document.createElement("div");
                this.placeholderEl = document.createElement("div");
                this.heightAutoObserverWrapperEl = document.createElement("div");
                this.heightAutoObserverEl = document.createElement("div");
                dist_addClasses(this.wrapperEl, this.classNames.wrapper);
                dist_addClasses(this.contentWrapperEl, this.classNames.contentWrapper);
                dist_addClasses(this.offsetEl, this.classNames.offset);
                dist_addClasses(this.maskEl, this.classNames.mask);
                dist_addClasses(this.contentEl, this.classNames.contentEl);
                dist_addClasses(this.placeholderEl, this.classNames.placeholder);
                dist_addClasses(this.heightAutoObserverWrapperEl, this.classNames.heightAutoObserverWrapperEl);
                dist_addClasses(this.heightAutoObserverEl, this.classNames.heightAutoObserverEl);
                while (this.el.firstChild) this.contentEl.appendChild(this.el.firstChild);
                this.contentWrapperEl.appendChild(this.contentEl);
                this.offsetEl.appendChild(this.contentWrapperEl);
                this.maskEl.appendChild(this.offsetEl);
                this.heightAutoObserverWrapperEl.appendChild(this.heightAutoObserverEl);
                this.wrapperEl.appendChild(this.heightAutoObserverWrapperEl);
                this.wrapperEl.appendChild(this.maskEl);
                this.wrapperEl.appendChild(this.placeholderEl);
                this.el.appendChild(this.wrapperEl);
                (_a = this.contentWrapperEl) === null || _a === void 0 ? void 0 : _a.setAttribute("tabindex", this.options.tabIndex.toString());
                (_b = this.contentWrapperEl) === null || _b === void 0 ? void 0 : _b.setAttribute("role", "region");
                (_c = this.contentWrapperEl) === null || _c === void 0 ? void 0 : _c.setAttribute("aria-label", this.options.ariaLabel);
            }
            if (!this.axis.x.track.el || !this.axis.y.track.el) {
                var track = document.createElement("div");
                var scrollbar = document.createElement("div");
                dist_addClasses(track, this.classNames.track);
                dist_addClasses(scrollbar, this.classNames.scrollbar);
                track.appendChild(scrollbar);
                this.axis.x.track.el = track.cloneNode(true);
                dist_addClasses(this.axis.x.track.el, this.classNames.horizontal);
                this.axis.y.track.el = track.cloneNode(true);
                dist_addClasses(this.axis.y.track.el, this.classNames.vertical);
                this.el.appendChild(this.axis.x.track.el);
                this.el.appendChild(this.axis.y.track.el);
            }
            SimpleBarCore.prototype.initDOM.call(this);
            this.el.setAttribute("data-simplebar", "init");
        };
        SimpleBar.prototype.unMount = function() {
            SimpleBarCore.prototype.unMount.call(this);
            SimpleBar.instances["delete"](this.el);
        };
        SimpleBar.initHtmlApi = function() {
            this.initDOMLoadedElements = this.initDOMLoadedElements.bind(this);
            if (typeof MutationObserver !== "undefined") {
                this.globalObserver = new MutationObserver(SimpleBar.handleMutations);
                this.globalObserver.observe(document, {
                    childList: true,
                    subtree: true
                });
            }
            if (document.readyState === "complete" || document.readyState !== "loading" && !document.documentElement.doScroll) window.setTimeout(this.initDOMLoadedElements); else {
                document.addEventListener("DOMContentLoaded", this.initDOMLoadedElements);
                window.addEventListener("load", this.initDOMLoadedElements);
            }
        };
        SimpleBar.handleMutations = function(mutations) {
            mutations.forEach((function(mutation) {
                mutation.addedNodes.forEach((function(addedNode) {
                    if (addedNode.nodeType === 1) if (addedNode.hasAttribute("data-simplebar")) !SimpleBar.instances.has(addedNode) && document.documentElement.contains(addedNode) && new SimpleBar(addedNode, dist_getOptions(addedNode.attributes)); else addedNode.querySelectorAll("[data-simplebar]").forEach((function(el) {
                        if (el.getAttribute("data-simplebar") !== "init" && !SimpleBar.instances.has(el) && document.documentElement.contains(el)) new SimpleBar(el, dist_getOptions(el.attributes));
                    }));
                }));
                mutation.removedNodes.forEach((function(removedNode) {
                    var _a;
                    if (removedNode.nodeType === 1) if (removedNode.getAttribute("data-simplebar") === "init") !document.documentElement.contains(removedNode) && ((_a = SimpleBar.instances.get(removedNode)) === null || _a === void 0 ? void 0 : _a.unMount()); else Array.prototype.forEach.call(removedNode.querySelectorAll('[data-simplebar="init"]'), (function(el) {
                        var _a;
                        !document.documentElement.contains(el) && ((_a = SimpleBar.instances.get(el)) === null || _a === void 0 ? void 0 : _a.unMount());
                    }));
                }));
            }));
        };
        SimpleBar.instances = new WeakMap;
        return SimpleBar;
    }(SimpleBarCore);
    if (dist_canUseDOM) SimpleBar.initHtmlApi();
    if (document.querySelectorAll("[data-simplebar]").length) document.querySelectorAll("[data-simplebar]").forEach((scrollBlock => {
        new SimpleBar(scrollBlock, {
            autoHide: false
        });
    }));
    let addWindowScrollEvent = false;
    setTimeout((() => {
        if (addWindowScrollEvent) {
            let windowScroll = new Event("windowScroll");
            window.addEventListener("scroll", (function(e) {
                document.dispatchEvent(windowScroll);
            }));
        }
    }), 0);
    class DynamicAdapt {
        constructor(type) {
            this.type = type;
        }
        init() {
            this.оbjects = [];
            this.daClassname = "_dynamic_adapt_";
            this.nodes = [ ...document.querySelectorAll("[data-da]") ];
            this.nodes.forEach((node => {
                const data = node.dataset.da.trim();
                const dataArray = data.split(",");
                const оbject = {};
                оbject.element = node;
                оbject.parent = node.parentNode;
                const parentSelectorMatch = dataArray[0].trim().match(/^\{(.+)\}(?:\s*(.+)?)$/);
                if (parentSelectorMatch) {
                    const parentSelector = parentSelectorMatch[1].trim();
                    const childSelector = parentSelectorMatch[2] ? parentSelectorMatch[2].trim() : null;
                    const parentElement = node.closest(parentSelector);
                    if (parentElement) оbject.destination = childSelector ? parentElement.querySelector(childSelector) : parentElement;
                } else оbject.destination = document.querySelector(`${dataArray[0].trim()}`);
                оbject.breakpoint = dataArray[1] ? dataArray[1].trim() : "767.98";
                оbject.place = dataArray[2] ? dataArray[2].trim() : "last";
                оbject.index = this.indexInParent(оbject.parent, оbject.element);
                this.оbjects.push(оbject);
            }));
            this.arraySort(this.оbjects);
            this.mediaQueries = this.оbjects.map((({breakpoint}) => `(${this.type}-width: ${breakpoint / 16}em),${breakpoint}`)).filter(((item, index, self) => self.indexOf(item) === index));
            this.mediaQueries.forEach((media => {
                const mediaSplit = media.split(",");
                const matchMedia = window.matchMedia(mediaSplit[0]);
                const mediaBreakpoint = mediaSplit[1];
                const оbjectsFilter = this.оbjects.filter((({breakpoint}) => breakpoint === mediaBreakpoint));
                matchMedia.addEventListener("change", (() => {
                    this.mediaHandler(matchMedia, оbjectsFilter);
                }));
                this.mediaHandler(matchMedia, оbjectsFilter);
            }));
        }
        mediaHandler(matchMedia, оbjects) {
            if (matchMedia.matches) оbjects.forEach((оbject => {
                this.moveTo(оbject.place, оbject.element, оbject.destination);
            })); else оbjects.forEach((({parent, element, index}) => {
                if (element.classList.contains(this.daClassname)) this.moveBack(parent, element, index);
            }));
        }
        moveTo(place, element, destination) {
            if (!destination) return;
            element.classList.add(this.daClassname);
            if (place === "last" || place >= destination.children.length) {
                destination.append(element);
                return;
            }
            if (place === "first") {
                destination.prepend(element);
                return;
            }
            destination.children[place].before(element);
        }
        moveBack(parent, element, index) {
            element.classList.remove(this.daClassname);
            if (parent.children[index] !== void 0) parent.children[index].before(element); else parent.append(element);
        }
        indexInParent(parent, element) {
            return [ ...parent.children ].indexOf(element);
        }
        arraySort(arr) {
            if (this.type === "min") arr.sort(((a, b) => {
                if (a.breakpoint === b.breakpoint) {
                    if (a.place === b.place) return 0;
                    if (a.place === "first" || b.place === "last") return -1;
                    if (a.place === "last" || b.place === "first") return 1;
                    return 0;
                }
                return a.breakpoint - b.breakpoint;
            })); else {
                arr.sort(((a, b) => {
                    if (a.breakpoint === b.breakpoint) {
                        if (a.place === b.place) return 0;
                        if (a.place === "first" || b.place === "last") return 1;
                        if (a.place === "last" || b.place === "first") return -1;
                        return 0;
                    }
                    return b.breakpoint - a.breakpoint;
                }));
                return;
            }
        }
    }
    const da = new DynamicAdapt("max");
    da.init();
    function updateHeaderHeights() {
        const headerEl = document.querySelector("header.header");
        if (headerEl) {
            const headerHeight = headerEl.offsetHeight;
            document.documentElement.style.setProperty("--header-height", `${headerHeight}px`);
        }
    }
    window.addEventListener("load", (function() {
        updateHeaderHeights();
    }));
    window.addEventListener("resize", updateHeaderHeights);
    document.addEventListener("click", (function(e) {
        if (!e.target.closest(".catalog-header") && !e.target.closest("[data-catalog-header]") && document.documentElement.classList.contains("catalog-header-open")) document.documentElement.classList.remove("catalog-header-open"); else if (e.target.closest("[data-catalog-header]")) document.documentElement.classList.toggle("catalog-header-open");
        if (!e.target.closest(".filter-catalog") && !e.target.closest("[data-filter-button]") && document.documentElement.classList.contains("filter-open")) {
            document.documentElement.classList.remove("filter-open");
            bodyUnlock();
        } else if (e.target.closest("[data-filter-button]")) {
            document.documentElement.classList.add("filter-open");
            bodyLock();
        } else if (e.target.closest("[data-filter-close]")) {
            document.documentElement.classList.remove("filter-open");
            bodyUnlock();
        }
        const categoryHeader = document.querySelectorAll(".categories-header__item");
        categoryHeader.forEach((function(categoryHeader) {
            e.target.closest(".categories-header__item") === categoryHeader ? categoryHeader.classList.add("_active") : categoryHeader.classList.remove("_active");
        }));
        if (e.target.closest(".video-block__button")) {
            const videoButton = e.target.closest(".video-block__button");
            const videoBody = videoButton.closest(".video-block__body");
            const videoPlaceholder = videoBody.querySelector(".video-block__placeholder");
            if (videoBody && videoPlaceholder) {
                const codeVideo = videoBody.dataset.video;
                const urlVideo = `https://www.youtube.com/embed/${codeVideo}?rel=0&showinfo=0&autoplay=1`;
                const iframe = document.createElement("iframe");
                iframe.setAttribute("allowfullscreen", "");
                iframe.setAttribute("allow", `autoplay; encrypted-media`);
                iframe.setAttribute("src", urlVideo);
                videoPlaceholder.hidden = true;
                videoBody.appendChild(iframe);
            }
        }
    }));
    const numberInputs = document.querySelectorAll(".counter");
    if (numberInputs) numberInputs.forEach((wrapperInput => {
        const input = wrapperInput.querySelector("input");
        const btnPlus = wrapperInput.querySelector(".counter__btn--increment");
        const btnMinus = wrapperInput.querySelector(".counter__btn--decrement");
        const min = parseInt(wrapperInput.getAttribute("data-min"));
        const max = parseInt(wrapperInput.getAttribute("data-max"));
        const validateInput = () => {
            let value = parseInt(input.value);
            if (isNaN(value)) value = min;
            if (value < min) value = min; else if (value > max) value = max;
            input.value = value;
        };
        btnPlus.addEventListener("click", (function() {
            let newValue = parseInt(input.value) + 1;
            if (newValue <= max) input.value = newValue;
            input.dispatchEvent(new Event("input"));
        }));
        btnMinus.addEventListener("click", (function() {
            let newValue = parseInt(input.value) - 1;
            if (newValue >= min) input.value = newValue;
            input.dispatchEvent(new Event("input"));
        }));
        input.addEventListener("input", validateInput);
        input.addEventListener("blur", validateInput);
    }));
    const origSelect = document.querySelector("#category");
    if (origSelect) document.addEventListener("selectCallback", (e => {
        if (e.detail.select === origSelect) console.log(e.detail.select.selectedOptions[0]);
    }));
    menuInit();
    spoilers();
    tabs();
    showMore();
})();