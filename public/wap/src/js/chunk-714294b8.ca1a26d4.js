(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["chunk-714294b8"],{"1fdf":function(t,i,s){"use strict";var a=s("e2dc"),e=s.n(a);e.a},"302c":function(t,i,s){},3081:function(t,i,s){},"795f":function(t,i,s){"use strict";var a=s("302c"),e=s.n(a);e.a},9284:function(t,i,s){"use strict";var a=s("3081"),e=s.n(a);e.a},b831:function(t,i,s){"use strict";s.r(i);var a=function(){var t=this,i=t.$createElement,s=t._self._c||i;return s("div",{staticStyle:{height:"calc(100vh - 100px)"}},[s("el-scrollbar",{ref:"scrollMenuRef",staticStyle:{height:"100%"},attrs:{wrapStyle:"overflow-x:hidden;"}},[t.info.headimg?s("div",{staticClass:"wraper",class:{fixed:t.isfixed}},[s("img",{staticClass:"header-back",style:{filter:t.filter},attrs:{src:t.info.back_url}}),s("div",{ref:"topWrap",staticClass:"top-wrap"},[s("div",{staticClass:"user-info"},[s("el-image",{staticClass:"avatar",attrs:{src:t.info.headimg,fit:"cover"}}),s("h3",{staticClass:"title"},[t._v(t._s(t.info.type_name))]),s("h4",{staticClass:"time"},[t._v(t._s(t.info.username))])],1),s("div",{staticClass:"quantityList u-f-justify"},[s("div",{staticClass:"quantityItem u-f-justify"},[s("p",[t._v(t._s(t.$t("albumDetailsText1")))]),s("p",{staticClass:"quantityItemSum"},[t._v(t._s(t.info.post_count))])]),s("div",{staticClass:"quantityItem u-f-justify"},[s("p",[t._v(t._s(t.$t("albumDetailsText2")))]),s("p",{staticClass:"quantityItemSum"},[t._v(t._s(t.info.user_count))])]),s("div",{staticClass:"quantityItem u-f-justify"},[s("p",[t._v(t._s(t.$t("albumDetailsText3")))]),s("p",{staticClass:"quantityItemSum"},[t._v(t._s(t.info.min_price))])]),s("div",{staticClass:"quantityItem u-f-justify"},[s("p",[t._v(t._s(t.$t("albumDetailsText4")))]),s("p",{staticClass:"quantityItemSum"},[t._v(t._s(t.info.buy_count))])])]),s("div",{staticClass:"text"},[s("div",{staticClass:"textDiv",class:{textDiv1:t.moreText}},[t._v("\n\t\t\t\t\t\t"+t._s(t.info.desc)+"\n\t\t\t\t\t")]),t.info.desc.length>65?s("div",{staticClass:"moreText cursor",class:{moreTextRotation:t.moreText},on:{click:function(i){t.moreText=!t.moreText}}},[s("img",{attrs:{src:"https://ec.wexiang.vip/source/img1/5.png",alt:""}})]):t._e()])]),s("div",{staticClass:"body flex align-start"},[s("div",{staticClass:"mark-main flex-1"},[s("div",{staticClass:"tools flex align-center"},[s("div",{staticClass:"backspace u-f-justify cursor mr10",on:{click:function(i){return t.$router.go(-1)}}},[s("div",{staticStyle:{width:"29px",height:"22px"}},[s("img",{staticClass:"img",attrs:{src:"https://ec.wexiang.vip/source/img1/3.png",alt:""}})])]),s("div",{staticClass:"big-small flex align-center",staticStyle:{"margin-right":"10px"},on:{click:t.inShowClick}},[s("div",{staticClass:"sbc"},[s("div",[s("img",{staticClass:"img",attrs:{src:"https://ec.wexiang.vip/source/img/39.png",alt:""}})])])]),s("div",{staticClass:"big-small flex align-center"},[s("div",{staticClass:"big cursor",on:{click:function(i){t.amplification=!0}}},[s("div",{},[t.amplification?s("img",{attrs:{src:"https://ec.wexiang.vip/source/img2/13.png",alt:""}}):s("img",{attrs:{src:"https://ec.wexiang.vip/source/img2/14.png",alt:""}})])]),s("div",{staticClass:"small cursor",on:{click:function(i){t.amplification=!1}}},[s("div",[t.amplification?s("img",{attrs:{src:"https://ec.wexiang.vip/source/img2/15.png",alt:""}}):s("img",{attrs:{src:"https://ec.wexiang.vip/source/img2/16.png",alt:""}})])])]),s("el-select",{attrs:{placeholder:t.$t("albumDetailsText5"),"popper-class":"select","popper-append-to-body":!1},model:{value:t.value,callback:function(i){t.value=i},expression:"value"}},t._l(t.$t("albumDetailsText11"),function(t){return s("el-option",{key:t.value,attrs:{label:t.label,value:t.value}})}),1),s("div",{staticClass:"search-card flex-1"},[s("el-input",{attrs:{"prefix-icon":"el-icon-search",placeholder:t.$t("albumDetailsText6")},model:{value:t.search,callback:function(i){t.search=i},expression:"search"}})],1),t.info.is_my?s("div",{staticClass:"backspace u-f-justify cursor ml10",on:{click:t.setAlbum}},[s("div",{staticStyle:{width:"20px",height:"22px"}},[s("img",{staticClass:"img",attrs:{src:"https://ec.wexiang.vip/source/img1/4.png"}})])]):t._e()],1),s("div",{staticClass:"contentBottom u-f"},[s("div",[s("transition",{attrs:{name:"show"}},[t.inShow?s("div",{staticClass:"filtrate"},[t._l(t.$t("albumDetailsText12"),function(i,a){return s("div",{key:a,staticClass:"filtrateItem cursor"},[s("div",{staticClass:" u-f-jsb u-f u-f-item",staticStyle:{"padding-bottom":"20px"},on:{click:function(s){return t.titleClick(a,i)}}},[s("p",{staticClass:"filtrateTitle"},[t._v(t._s(i.name))]),s("div",{staticClass:"icon"},[s("img",{staticClass:"img",class:{titleItemFlag:!i.falg},attrs:{src:"https://ec.wexiang.vip/source/img/36.png",alt:""}})])]),s("el-collapse-transition",[i.falg?s("div",t._l(i.list,function(i,e){return s("div",{key:e,staticClass:"Item u-f-jsb u-f u-f-item ",on:{click:function(i){return t.filtrateTitleTrumpetItemClick(a,e)}}},[s("p",{staticClass:"filtrateTitleTrumpet"},[t._v(t._s(i.name))]),s("div",{class:{itemIcon:!i.falg},staticStyle:{"margin-right":"16px"}},[i.falg?s("img",{attrs:{src:"https://ec.wexiang.vip/source/img/37.png",alt:""}}):t._e()])])}),0):t._e()])],1)}),s("div",{staticClass:"filtrateItem cursor"},[s("div",{staticClass:" u-f-jsb u-f u-f-item",staticStyle:{"padding-bottom":"20px"},on:{click:function(i){t.priceFalg=!t.priceFalg}}},[s("p",{staticClass:"filtrateTitle"},[t._v(t._s(t.$t("albumDetailsText7")))]),s("div",{staticClass:"icon"},[s("img",{staticClass:"img",class:{titleItemFlag:!t.priceFalg},attrs:{src:"https://ec.wexiang.vip/source/img/36.png",alt:""}})])]),s("el-collapse-transition",[t.priceFalg?s("div",[s("div",[s("el-select",{staticStyle:{width:"100px","padding-left":"19px"},attrs:{placeholder:t.$t("albumDetailsText8"),"popper-class":"select","popper-append-to-body":!1},model:{value:t.value,callback:function(i){t.value=i},expression:"value"}},t._l(t.$t("albumDetailsText11"),function(t){return s("el-option",{key:t.value,attrs:{label:t.label,value:t.value}})}),1)],1),s("div",{staticClass:"u-f u-f-item Price"},[s("div",{staticClass:"connomPrice"},[s("el-input",{staticStyle:{background:"#101320"},attrs:{placeholder:t.$t("albumDetailsText9")}})],1),s("p",{staticClass:"PriceText"},[t._v("-")]),s("div",{staticClass:"connomPrice"},[s("el-input",{attrs:{placeholder:t.$t("albumDetailsText10")}})],1)])]):t._e()])],1)],2):t._e()])],1),s("div",[s("div",{staticClass:"article u-f-jsb ",class:{articleSoll:t.isfixed},style:{width:t.calculative}},[t._l(t.articleList,function(i,a){return s("div",{key:a,staticClass:"articleItem cursor",on:{click:function(s){return t.itemCLick(i)}}},[s("div",{staticClass:"articleItemImg",class:{amplification:t.amplification}},[2!=t.info.type?s("el-image",{staticClass:"img",staticStyle:{"border-radius":"10px"},attrs:{src:i.cover,fit:"cover"}}):t._e(),2==t.info.type?s("el-image",{staticClass:"img",staticStyle:{"border-radius":"10px"},attrs:{src:i.path,fit:"cover"}}):t._e()],1),s("div",{staticClass:"particulars"},[1==t.info.type?s("p",{staticClass:"articleItemName singleLine",class:{articleItemName1:t.amplification}},[t._v("\n\t\t\t\t\t\t\t\t\t\t\t"+t._s(i.title))]):t._e(),2==t.info.type?s("p",{staticClass:"articleItemName singleLine",class:{articleItemName1:t.amplification}},[t._v("\n\t\t\t\t\t\t\t\t\t\t\t"+t._s(i.name))]):t._e(),3==t.info.type?s("p",{staticClass:"articleItemName singleLine",class:{articleItemName1:t.amplification}},[t._v("\n\t\t\t\t\t\t\t\t\t\t\t"+t._s(i.game_name)+"\n\t\t\t\t\t\t\t\t\t\t")]):t._e(),s("p",{staticClass:"explain singleLine"},[t._v("#"+t._s(i.code))]),s("div",{staticClass:"u-f-item"},[s("div",[s("img",{staticClass:"img",attrs:{src:"https://ec.wexiang.vip/source/img/48.png",alt:""}})]),s("p",{staticClass:"articleItemPrice"},[t._v(t._s(i.price))])])])])}),t._l(5,function(i,a){return s("i",{staticClass:"articleIcon",class:{amplification:t.amplification}})})],2)])])])])]):t._e()])],1)},e=[],l={data:function(){return{info:{},headback:"https://ec.wexiang.vip/source/static/",avatar:"https://ec.wexiang.vip/source/static/a_01.png",type:[],filter:null,value:"",search:"",isfixed:!1,navIndex:0,inShow:!0,priceFalg:!0,articleList:[],amplification:!1,moreText:!1,wrapScrollTop:0}},mounted:function(){this.$refs.scrollMenuRef.wrap.addEventListener("scroll",this.scrollMenu)},activated:function(){var t=this;this.getcateinfo(),this.$nextTick(function(){t.$refs.scrollMenuRef.wrap.scrollTo(0,t.wrapScrollTop,0)})},computed:{navigationFlag:function(){return this.$store.getters.navigationFlag},calculative:function(){return this.navigationFlag?this.inShow?"calc(100vw - (580px))":"calc(100vw - 280px )":this.inShow?"calc(100vw - 415px)":"calc(100vw - 125px)"}},methods:{scrollMenu:function(){var t=this;this.times&&clearTimeout(this.times),this.times=setTimeout(function(){var i=t.$refs.scrollMenuRef.wrap.scrollTop;t.$refs.scrollMenuRef.wrap.scrollHeight;t.wrapScrollTop=i,t.filter="blur(".concat(i/40,"px)"),i>=t.$refs.topWrap.offsetHeight-20&&i>680?t.isfixed=!0:t.isfixed=!1},10)},navChangeClick:function(t){this.navIndex=t},inShowClick:function(){this.inShow=!this.inShow},titleClick:function(t,i){this.list[t].falg=!this.list[t].falg},filtrateTitleTrumpetItemClick:function(t,i){this.list[t].list[i].falg=!this.list[t].list[i].falg},getcateinfo:function(){var t=this;this.$post({method:"articles.getcateinfo",type_id:this.$route.query.id}).then(function(i){t.info=i.data.data.info,t.articleList=i.data.data.list})},itemCLick:function(t){var i=t.id,s="";1==this.info.type?s="colleagues":2==this.info.type?s="audio":3==this.info.type?(s="game",i=t.g_id):4==this.info.type&&(s="ikon"),this.$router.push({path:"/explore/particulars",query:{id:i,type:s}})},setAlbum:function(){this.$router.push({path:"/framer/createAlbum",query:{id:this.info.id}})}}},c=l,n=(s("1fdf"),s("795f"),s("9284"),s("2877")),r=Object(n["a"])(c,a,e,!1,null,"061ba5f1",null);i["default"]=r.exports},e2dc:function(t,i,s){}}]);