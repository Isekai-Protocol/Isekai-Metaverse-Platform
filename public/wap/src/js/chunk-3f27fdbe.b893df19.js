(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["chunk-3f27fdbe"],{3477:function(t,e,i){"use strict";i.r(e);var s=function(){var t=this,e=t.$createElement,i=t._self._c||e;return i("div",{ref:"recommend",staticClass:"recommend",on:{scroll:t.scroll}},[i("audio",{ref:"audio",staticStyle:{display:"none"},attrs:{src:t.musicUrl,controls:"controls"}}),i("slideshow",{ref:"slideshow",attrs:{slideshowList:t.slideshowList},on:{folioItemClick:t.folioItemClick,itemClick:t.slideshowItemClick}}),i("div",{staticClass:"folio u-f-justify"},t._l(t.slideshowList,function(e,s){return i("div",{key:s,staticClass:"folioItem cursor",class:{folioItemActive:s==t.active},on:{click:function(e){return t.folioItemClick1(s)}}})}),0),i("selectAlbum",{attrs:{selectAlbumList:t.selectAlbumList}}),i("recommended",{attrs:{recommendedList:t.recommendedList}}),i("thirdParty",{attrs:{thirdPartyList:t.thirdPartyList}}),i("frequency",{ref:"frequency",attrs:{frequencyList:t.frequencyList,frequencyList1:t.frequencyList1},on:{music:t.play,stopMusic:t.pause}}),i("Article",{ref:"Article",attrs:{articleList:t.articleList}}),i("game",{ref:"game",attrs:{gameList:t.gameList}}),i("ikon",{ref:"ikon",attrs:{ikonList:t.ikonList},on:{titleClick:t.ikonTitleClick}})],1)},a=[],o=i("75fc"),n=i("5b7c"),c=i("2667"),r=i("cf22"),l=i("3ad9"),d=i("565e"),u=i("d018"),h=i("f36e"),f=i("0233"),m={components:{slideshow:n["a"],selectAlbum:c["a"],recommended:r["a"],frequency:d["a"],thirdParty:l["a"],Article:u["a"],game:h["a"],ikon:f["a"]},data:function(){return{slideshowList:[],selectAlbumList:[],thirdPartyList:[],recommendedList:[],frequencyList:[],frequencyList1:[],articleList:[],gameList:[],ikonList:[],audio:{playing:!1,currentTime:0,maxTime:0,minTime:0,step:.1},musicUrl:null,page:1,count:0,getIkonFalg:!1,active:0,saveY:0,timer:null}},created:function(){this.getSlideshow(),this.getSelectAlbum(),this.getThirdParty(),this.getRecommended()},activated:function(){var t=this;this.$nextTick(function(){t.$refs.recommend.scrollTo(0,t.saveY,0)})},methods:{folioItemClick:function(t){this.active=t},folioItemClick1:function(t){0==t?(this.$refs.slideshow.$refs.swiper.swiper.slideTo(1,0),this.$refs.slideshow.$refs.swiper.swiper.slidePrev()):this.$refs.slideshow.$refs.swiper.swiper.slideTo(t++)},slideshowItemClick:function(t){this.slideshowList[this.active].id==t.id&&window.open(t.val)},getSlideshow:function(){var t=this;this.$get({method:"advert.getAdvertList",code:"tpl1_slider",page:1,limit:10},!1).then(function(e){e.data.status&&(t.slideshowList=e.data.data.list)})},getSelectAlbum:function(){var t=this;this.$get({method:"articles.getArticleType",page:1,limit:12},!1).then(function(e){e.data.status&&(t.selectAlbumList=e.data.data.list)})},getThirdParty:function(){var t=this;this.$get({method:"advert.getAdvertList",code:"tpl1_class_banner1",page:1,limit:30},!1).then(function(e){e.data.status&&(t.thirdPartyList=e.data.data.list)})},getRecommended:function(){var t=this;this.$get({method:"user.getrecommenduser",is_recommend:1,p:1,n:30},!1).then(function(e){e.data.status&&(t.recommendedList=e.data.data.list)})},getFrequency:function(){var t=this;this.$get({method:"notice.audiolist",page:1,limit:14,type:1},!1).then(function(e){t.frequencyList=e.data.data.list}),this.$get({method:"notice.audiolist",page:1,limit:14,type:2},!1).then(function(e){t.frequencyList1=e.data.data.list})},getArticle:function(){var t=this;this.$get({method:"articles.getArticleList",page:1,limit:6},!1).then(function(e){e.data.status&&(t.articleList=e.data.data.list)})},getGame:function(){var t=this;this.$post({method:"notice.getgameList",page:1,limit:6},!1).then(function(e){e.data.status&&(t.gameList=e.data.data.list)})},getIkon:function(){var t=this,e=arguments.length>0&&void 0!==arguments[0]?arguments[0]:1;this.getIkonFalg=!1,this.$post({method:"images.getpaintings",page:this.page,limit:30,type:e,is_my:0},!1).then(function(e){e.data.status&&(t.getIkonFalg=!0,t.count=e.data.data.count,t.ikonList=[].concat(Object(o["a"])(t.ikonList),Object(o["a"])(e.data.data.list)))})},ikonTitleClick:function(t){this.ikonList=[],this.page=1,this.getIkon(t)},loadmore:function(){this.ikonList.length<this.count&&this.getIkonFalg&&(this.page++,this.getIkon(this.$refs.ikon.TitlePitch+1))},play:function(t){var e=this;this.musicUrl=t,this.$nextTick(function(){e.$refs.audio.play()})},pause:function(){this.$refs.audio.pause()},scroll:function(t){var e=this;this.timer&&clearTimeout(this.timer),this.timer=setTimeout(function(){var i=t.target.scrollTop;e.saveY=i,t.target.scrollTop>=e.$refs.frequency.$el.offsetTop-1500&&!e.frequencyList.length&&e.getFrequency(),t.target.scrollTop>=e.$refs.Article.$el.offsetTop-1500&&!e.articleList.length&&e.getArticle(),t.target.scrollTop>=e.$refs.game.$el.offsetTop-1500&&!e.gameList.length&&e.getGame(),t.target.scrollTop>=e.$refs.ikon.$el.offsetTop-1500&&!e.ikonList.length&&e.getIkon();var s=t.target.clientHeight,a=t.target.scrollHeight;e.saveY=i,i+s+100>=a&&e.loadmore()},200)}}},g=m,p=(i("6cb2"),i("2877")),L=Object(p["a"])(g,s,a,!1,null,"2c12a49d",null);e["default"]=L.exports},"6cb2":function(t,e,i){"use strict";var s=i("e319"),a=i.n(s);a.a},e319:function(t,e,i){}}]);