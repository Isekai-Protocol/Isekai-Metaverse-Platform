(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["chunk-22220546"],{"268f":function(t,e,o){t.exports=o("fde4")},"32a6":function(t,e,o){var n=o("241e"),a=o("c3a1");o("ce7e")("keys",function(){return function(t){return a(n(t))}})},"3be2":function(t,e,o){},"454f":function(t,e,o){o("46a7");var n=o("584a").Object;t.exports=function(t,e,o){return n.defineProperty(t,e,o)}},"46a7":function(t,e,o){var n=o("63b6");n(n.S+n.F*!o("8e60"),"Object",{defineProperty:o("d9f6").f})},"5b56":function(t,e,o){"use strict";o.r(e);var n=function(){var t=this,e=t.$createElement,o=t._self._c||e;return o("div",{staticClass:"login"},[o("div",{staticClass:"bjcImg"}),o("div",{staticClass:"content"},[o("div",{staticClass:"logo"},[t.jshopconf.login_logo?o("el-image",{staticClass:"img",attrs:{src:t.jshopconf.login_logo,fit:"contain"}}):t._e()],1),o("p",{staticClass:"Log"},[t._v(t._s(t.$t("loginText1")))]),o("div",{staticClass:"input Email"},[1==t.form.verify_type?o("input",{directives:[{name:"model",rawName:"v-model",value:t.form.email,expression:"form.email"}],attrs:{type:"text",placeholder:t.$t("loginText2")},domProps:{value:t.form.email},on:{input:function(e){e.target.composing||t.$set(t.form,"email",e.target.value)}}}):o("input",{directives:[{name:"model",rawName:"v-model",value:t.form.mobile,expression:"form.mobile"}],attrs:{type:"text",placeholder:t.$t("loginText3")},domProps:{value:t.form.mobile},on:{input:function(e){e.target.composing||t.$set(t.form,"mobile",e.target.value)}}}),o("p",{staticClass:"method cursor",on:{click:function(e){t.form.verify_type=1==t.form.verify_type?2:1}}},[t._v("\n\t\t\t\t"+t._s(1==t.form.verify_type?t.$t("loginText12"):t.$t("loginText13"))+"\n\t\t\t")])]),o("div",{staticClass:"input Password"},[o("input",{directives:[{name:"model",rawName:"v-model",value:t.form.password,expression:"form.password"}],attrs:{type:"password",placeholder:t.$t("loginText14")},domProps:{value:t.form.password},on:{input:function(e){e.target.composing||t.$set(t.form,"password",e.target.value)}}})]),o("el-collapse-transition",[t.mistake?o("div",[o("p",{staticClass:"mistake"},[t._v(t._s(t.mistake))])]):t._e()]),o("div",{staticClass:"LogIn cursor",on:{click:t.LogClick}},[t._v("\n\t\t\t"+t._s(t.$t("loginText1"))+"\n\t\t")]),o("div",{staticClass:"operation u-f-justify  mailbox cursor",on:{click:t.MetaMaskClick}},[t._m(0),t._v("\n\t\t\t"+t._s(t.$t("loginText4"))+"\n\t\t")]),o("div",{staticClass:"u-f-jsb skip"},[o("p",[t._v(t._s(t.$t("loginText5")))]),o("p",{on:{click:t.skip}},[t._v(t._s(t.$t("loginText6")))])]),o("p",{staticClass:"hint"},[t._v("\n\t\t\t"+t._s(t.$t("loginText7"))),o("span",[t._v(t._s(t.$t("loginText8")))]),t._v(t._s(t.$t("loginText9"))),o("span",[t._v(t._s(t.$t("loginText10")))]),t._v(t._s(t.$t("loginText11"))+"\n\t\t")])],1),o("div",{staticClass:"support u-f cursor"},[t._m(1),o("p",[t._v(t._s(t.$t("loginText15")))])]),o("popUp",{attrs:{showDialog:t.popUpHint,borderBottom:!1,top:!1}},[o("p",{staticClass:"popUpHint"},[t._v("您的 MetaMask 钱包已在运行中，请在浏览器打开 MetaMask 扩展进行登录")]),o("div",{staticClass:"popUpHintOperation cursor",on:{click:function(e){t.popUpHint=!1}}},[t._v("确认")])]),o("popUp",{attrs:{showDialog:t.register,borderBottom:!1,top:!1}},[o("p",{staticClass:"popUpHint"},[t._v("当前 MetaMask 钱包还未绑定注册过 Isekai 账号，点击前往进行注册\t")]),o("div",{staticClass:"popUpHintOperation cursor",on:{click:t.registerSkip}},[t._v("确认")])])],1)},a=[function(){var t=this,e=t.$createElement,o=t._self._c||e;return o("div",{staticClass:"Metamask"},[o("img",{staticClass:"img",attrs:{src:"https://ec.wexiang.vip/source/img1/30.png",alt:""}})])},function(){var t=this,e=t.$createElement,o=t._self._c||e;return o("div",{staticStyle:{width:"28px",height:"29px"}},[o("img",{staticClass:"img",attrs:{src:"https://ec.wexiang.vip/source/img1/31.png",alt:""}})])}],i=(o("a481"),o("cebc")),s=o("c030"),r=o("50c5"),c={components:{popUp:r["a"]},data:function(){return{jshopconf:{},form:{password:"",platform:5,type:1,email:"",verify_type:1,mobile:""},mistake:"",bjc:"https://ec.wexiang.vip/source/img1/68.png",accountaddress:"",signature:"",popUpHint:!1,register:!1}},watch:{mistake:{handler:function(t){var e=this;t&&setTimeout(function(){e.mistake=""},2e3)}}},created:function(){this.getJshopconf(),this.getSaveuseract()},methods:{skip:function(){this.$router.push({path:"/fullScreen/register/index"})},LogClick:function(){var t=this;this.$post(Object(i["a"])({method:"user.login"},this.form)).then(function(e){e.data.status?(t.$store.commit("account/onToken",e.data.data),localStorage.setItem("token",e.data.data),t.$post({method:"user.info",token:e.data.data}).then(function(e){localStorage.setItem("userInfo",JSON.stringify(e.data.data)),t.$router.replace("/explore/recommend")})):t.mistake=e.data.msg})},MetaMaskClick:function(){var t=this,e=this;ethereum.enable().then(function(t){var o=new s["a"].providers.Web3Provider(web3.currentProvider);o.getNetwork().then(function(t){if(1!=t["chainId"])return alert("请更换区块链"),!1;o.listAccounts().then(function(t){e.accountaddress=t[0],localStorage.setItem("accountaddress",t[0]),e.$post({method:"user.wallectexist",address:t[0]}).then(function(t){t.data.data?(e.$store.commit("account/onToken",t.data.data),localStorage.setItem("token",t.data.data),e.$post({method:"user.info",token:t.data.data}).then(function(t){localStorage.setItem("userInfo",JSON.stringify(t.data.data)),e.$router.replace("/explore/recommend")})):e.register=!0})})})}).catch(function(e){-32002==e.code&&(t.popUpHint=!0)})},registerSkip:function(){this.$router.push({path:"/fullScreen/register/register",query:{type:"wallet"}})},getJshopconf:function(){var t=this;this.$post({method:"common.jshopconf"}).then(function(e){localStorage.setItem("jshopconf",JSON.stringify(e.data)),t.jshopconf=e.data})},getSaveuseract:function(){this.$post({method:"common.saveuseract",page_id:"10002005",action_id:"9005001",page_name:"登录页界面",position:"user_login",action_name:"用户登录/注册"}).then(function(t){})}}},p=c,l=(o("e237"),o("2877")),u=Object(l["a"])(p,n,a,!1,null,"3ebfdf11",null);e["default"]=u.exports},"85f2":function(t,e,o){t.exports=o("454f")},"8aae":function(t,e,o){o("32a6"),t.exports=o("584a").Object.keys},a4bb:function(t,e,o){t.exports=o("8aae")},bf90:function(t,e,o){var n=o("36c3"),a=o("bf0b").f;o("ce7e")("getOwnPropertyDescriptor",function(){return function(t,e){return a(n(t),e)}})},ce7e:function(t,e,o){var n=o("63b6"),a=o("584a"),i=o("294c");t.exports=function(t,e){var o=(a.Object||{})[t]||Object[t],s={};s[t]=e(o),n(n.S+n.F*i(function(){o(1)}),"Object",s)}},cebc:function(t,e,o){"use strict";var n=o("268f"),a=o.n(n),i=o("e265"),s=o.n(i),r=o("a4bb"),c=o.n(r),p=o("85f2"),l=o.n(p);function u(t,e,o){return e in t?l()(t,e,{value:o,enumerable:!0,configurable:!0,writable:!0}):t[e]=o,t}function f(t){for(var e=1;e<arguments.length;e++){var o=null!=arguments[e]?arguments[e]:{},n=c()(o);"function"===typeof s.a&&(n=n.concat(s()(o).filter(function(t){return a()(o,t).enumerable}))),n.forEach(function(e){u(t,e,o[e])})}return t}o.d(e,"a",function(){return f})},e237:function(t,e,o){"use strict";var n=o("3be2"),a=o.n(n);a.a},e265:function(t,e,o){t.exports=o("ed33")},ed33:function(t,e,o){o("014b"),t.exports=o("584a").Object.getOwnPropertySymbols},fde4:function(t,e,o){o("bf90");var n=o("584a").Object;t.exports=function(t,e){return n.getOwnPropertyDescriptor(t,e)}}}]);