(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["chunk-56e0da82"],{"0ce8":function(t,i,s){},a4da:function(t,i,s){"use strict";s.r(i);var e=function(){var t=this,i=t.$createElement,s=t._self._c||i;return s("div",{staticClass:"contentMain"},["/explore/recommend"==t.routePath?s("div",{staticClass:"bjcImg",style:{zoom:t.zoom>1?1:t.zoom}},[s("video",{staticClass:"head_video",attrs:{"data-v-71d7d4ec":"",src:"https://ec.wexiang.vip/source/img/ft_h.webm",autoplay:"autoplay",loop:"loop",muted:"muted"},domProps:{muted:!0}})]):t._e(),s("div",{staticClass:"u-f"},[s("div",{staticClass:"letf u-f u-f-jsb u-f-column ",class:{unfoldLetf:!t.unfold,unfoldRight:t.unfold,drawCard:t.drawCard}},[s("div",[s("div",{staticClass:"bjc",class:{unfoldLetf:!t.unfold,unfoldRight:t.unfold}}),s("transition",{attrs:{name:"el-zoom-in-center"}},[s("div",{directives:[{name:"show",rawName:"v-show",value:t.unfold&&t.framer,expression:"unfold&&framer"}],staticClass:"creation cursor",on:{click:t.createClick}},[t._v("\n\t\t\t\t\t\t"+t._s(t.$t("Creatework"))+"\n\t\t\t\t\t")])]),t._l(t.framer?t.$t("writingCenter"):t.titleList,function(i,e){return s("div",{key:e,on:{click:function(s){return t.titleClick(i,e)}}},[i.falg?s("div",[s("div",{staticClass:"titleItem u-f-item  cursor u-f-jsb",class:{pitchTitle:t.routePath.substring(0,5)==i.url.substring(0,5)}},[s("div",{staticClass:"u-f-item"},[s("div",[s("img",{attrs:{src:i.icon,alt:""}})]),t.unfold?s("div",{staticClass:"titleText"},[t._v("\n\t\t\t\t\t\t\t\t\t"+t._s(i.name)+"\n\t\t\t\t\t\t\t\t")]):t._e()]),t.unfold?s("div",[s("i",{class:t.routePath.substring(0,5)==i.url.substring(0,5)?"el-icon-arrow-down":"el-icon-arrow-up",staticStyle:{color:"#fff","font-size":"22px"}})]):t._e()]),s("el-collapse-transition",[t.routePath.substring(0,5)==i.url.substring(0,5)&&t.unfold?s("div",[s("div",{staticClass:"titleList"},t._l(i.list,function(i,e){return s("div",{key:e,staticClass:"titleListItem cursor",class:{titleListItemListpitch:t.routePath==i.url},on:{click:function(s){return s.stopPropagation(),t.skip(i)}}},[t._v("\n\t\t\t\t\t\t\t\t\t\t"+t._s(i.name)+"\n\t\t\t\t\t\t\t\t\t")])}),0)]):t._e()])],1):s("div",{staticClass:"titleItem u-f-item cursor",class:{pitchTitle:t.routePath.substring(0,10)==i.url.substring(0,10)}},[s("div",{staticClass:"titleItemImg",class:{overturn:!t.unfold&&0==e}},[s("img",{attrs:{src:i.icon,alt:""}})]),t.unfold?s("div",{staticClass:"titleText"},[t._v("\n\t\t\t\t\t\t\t"+t._s(i.name)+"\n\t\t\t\t\t\t")]):t._e()])])})],2),s("transition",{attrs:{name:"el-zoom-in-center"}},[t.unfold&&!t.framer?s("div",{staticClass:"letfBottom"},[s("div",{staticClass:"u-f"},[s("div",{staticClass:"letfBottomConnom u-f-justify cursor",on:{click:function(i){return t.skipOutside("https://twitter.com/IsekaiMetaverse?t=1JYhVWL0A7YwOfiRaKP7Ig&s=09")}}},[s("img",{attrs:{src:"https://ec.wexiang.vip/source/img/25.png",alt:""}})]),s("div",{staticClass:"letfBottomConnom u-f-justify cursor",on:{click:function(i){return t.skipOutside("https://link.medium.com/q7L0qg43hsb")}}},[s("img",{attrs:{src:"https://ec.wexiang.vip/source/img/26.png",alt:""}})]),s("div",{staticClass:"letfBottomConnom u-f-justify cursor",on:{click:function(i){return t.skipOutside("https://discord.gg/jD6PuV54tj")}}},[s("img",{attrs:{src:"https://ec.wexiang.vip/source/img/24.png",alt:""}})])]),s("div",{staticClass:"Email u-f-item u-f-justify cursor",on:{click:t.dialogVisibleClick}},[s("div",[s("img",{attrs:{src:"https://ec.wexiang.vip/source/img/27.png",alt:""}})]),s("p",{staticClass:"EmailText"},[t._v(t._s(t.$t("subscription")))])])]):t._e()])],1),s("div",{ref:"right",staticClass:"right",class:{rightUnfoldLetf:!t.unfold,rightUnfoldRight:t.unfold,rightUnfoldRightDrawCard:t.drawCard},style:{width:t.unfold?"calc(100vw - 240px)":"calc(100vw - 80px)",backgroundColor:"/explore/recommend"==t.routePath?"":"#090B16"}},[s("div",{staticStyle:{width:"100%",height:"100%"},style:{zoom:t.zoom>1?1:t.zoom}},[s("router-view")],1)])]),s("popUp",{attrs:{title:"",borderBottom:!1,showDialog:t.dialogVisible},on:{showDialogClick:function(i){t.dialogVisible=!1}}},[s("div",{staticClass:"pop-msg"},[s("img",{staticClass:"icon",attrs:{src:"https://ec.wexiang.vip/source/static//img/email.png",alt:""}}),s("h3",[t._v(t._s(t.$t("subscription")))]),s("h4",{staticClass:"describe"},[t._v(t._s(t.$t("Emailinstructions")))]),s("div",{staticClass:"email-input u-f-item u-f-jsb"},[s("input",{directives:[{name:"model",rawName:"v-model",value:t.subscriptionEmail,expression:"subscriptionEmail"}],staticClass:"input flex-1",staticStyle:{width:"300px"},attrs:{type:"text",placeholder:t.$t("subscriptionPlaceholder")},domProps:{value:t.subscriptionEmail},on:{input:function(i){i.target.composing||(t.subscriptionEmail=i.target.value)}}}),s("div",{staticClass:"btn cursor",on:{click:t.subscriptionClick}},[t._v(t._s(t.$t("subscriptionText")))])]),s("transition",{attrs:{name:"el-zoom-in-center"}},[t.hintText?s("p",{staticClass:"hintText"},[t._v(t._s(t.hintText))]):t._e()])],1)])],1)},o=[],n=(s("57e7"),s("50c5")),a={components:{popUp:n["a"]},data:function(){return{dialogVisible:!1,framer:!1,hintText:"",subscriptionEmail:"",zoom:1,routePath:"",saveY:0,times:null}},watch:{hintText:{handler:function(t){var i=this;t&&setTimeout(function(){i.hintText=""},2e3)}}},computed:{unfold:function(){return this.$store.getters.navigationFlag},drawCard:function(){return this.$store.getters.drawCard},titleList:function(){return this.$store.getters.titleList}},created:function(){this.routePath=this.$route.fullPath,1==this.$route.fullPath.indexOf("framer")?this.framer=!0:this.framer=!1},activated:function(){var t=this;this.$nextTick(function(){t.routePath=t.$route.fullPath,1==t.$route.fullPath.indexOf("framer")?t.framer=!0:t.framer=!1,t.zoomFn()})},mounted:function(){var t=this;this.$store.commit("account/onCurrentScreenSize",document.body.clientWidth),this.zoom=document.body.clientWidth/1e3*.4,window.addEventListener("resize",function(){return function(){t.zoomFn()}()},!1)},methods:{titleClick:function(t,i){0!=i?this.skip(t):this.$store.commit("account/onnavigationFlag",!this.unfold)},skip:function(t){var i=this;this.$nextTick(function(){i.routePath=t.url}),this.$router.push({path:t.url})},dialogVisibleClick:function(){var t=this;localStorage.getItem("token")?this.$post({method:"user.info"}).then(function(i){t.subscriptionEmail=i.data.data.reserve_email,t.dialogVisible=!0}):this.$store.commit("account/onRegister",!0)},subscriptionClick:function(){var t=this;this.$post({method:"user.reserveemail",email:this.subscriptionEmail}).then(function(i){i.data.status?(t.$message({message:i.data.msg,type:"success"}),t.dialogVisible=!1):t.hintText=i.data.msg})},zoomFn:function(){var t=this;this.$nextTick(function(){t.$store.commit("account/onCurrentScreenSize",document.body.clientWidth);var i=t.routePath.substring(0,20),s="/bazaar/albumDetails",e="/userCenter",o="/explore/albumDetails";i==s.substring(0,20)||t.routePath.substring(0,8)==e.substring(0,8)||i==o.substring(0,20)?t.zoom=1:t.zoom=document.body.clientWidth/1e3*.4})},createClick:function(){this.$router.push({path:"/fullScreen/createWork"})},skipOutside:function(t){window.open(t)}}},r=a,c=(s("c24b"),s("2877")),l=Object(c["a"])(r,e,o,!1,null,"14d7b03a",null);i["default"]=l.exports},c24b:function(t,i,s){"use strict";var e=s("0ce8"),o=s.n(e);o.a}}]);