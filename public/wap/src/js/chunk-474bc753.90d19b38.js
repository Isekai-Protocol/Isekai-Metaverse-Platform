(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["chunk-474bc753"],{"0062":function(t,e,i){"use strict";i.r(e);var n=function(){var t=this,e=t.$createElement,i=t._self._c||e;return i("div",{staticClass:"mailboxVerification"},[i("div",{staticClass:"content"},[i("div",{staticClass:"logo"},[i("el-image",{staticClass:"img",attrs:{src:t.jshopconf.login_logo,fit:"contain"}})],1),i("h2",[t._v(t._s(1==t.from.verify_type?t.$t("mailboxVerificationText1"):t.$t("mailboxVerificationText11")))]),i("p",{staticClass:"hint"},[t._v(t._s(1==t.from.verify_type?t.$t("mailboxVerificationText2"):t.$t("mailboxVerificationText22")))]),i("p",{staticClass:"hint1"},[t._v(t._s(1==t.from.verify_type?t.$t("mailboxVerificationText3"):t.$t("mailboxVerificationText33")))]),i("div",{staticClass:"u-f"},[i("div",{staticClass:"input First"},[i("input",{directives:[{name:"model",rawName:"v-model",value:t.from.code,expression:"from.code"}],attrs:{type:"text",placeholder:t.$t("mailboxVerificationText4")},domProps:{value:t.from.code},on:{input:function(e){e.target.composing||t.$set(t.from,"code",e.target.value)}}})]),i("div",{staticClass:"button cursor",on:{click:t.registerClick}},[t._v("\n\t\t\t\t"+t._s(1==t.from.verify_type?t.$t("mailboxVerificationText5"):t.$t("mailboxVerificationText55"))+"\n\t\t\t")])]),i("el-collapse-transition",[t.mistake?i("p",{staticClass:"mistake"},[t._v(t._s(t.mistake))]):t._e()])],1),i("div",{staticClass:"support u-f cursor"},[t._m(0),i("p",[t._v(t._s(t.$t("mailboxVerificationText6")))])])])},a=[function(){var t=this,e=t.$createElement,i=t._self._c||e;return i("div",{staticStyle:{width:"28px",height:"29px"}},[i("img",{staticClass:"img",attrs:{src:"https://ec.wexiang.vip/source/img1/31.png",alt:""}})])}],o=i("cebc"),r=(i("a481"),{data:function(){return{jshopconf:JSON.parse(localStorage.getItem("jshopconf")),from:this.$store.state.account.RegistrationParameters,mistake:""}},created:function(){this.$store.state.account.RegistrationParameters||this.$router.replace("/fullScreen/register/login")},watch:{mistake:{handler:function(t){var e=this;""!=t&&setTimeout(function(){e.mistake=""},2e3)}}},methods:{registerClick:function(){var t=this;console.log(this.from),this.$post(Object(o["a"])({method:"user.login"},this.from)).then(function(e){e.data.status?(t.$store.commit("account/onToken",e.data.data),localStorage.setItem("token",e.data.data),t.$post({method:"user.info",token:e.data.data}).then(function(e){localStorage.setItem("userInfo",JSON.stringify(e.data.data)),t.$router.replace({path:"/fullScreen/register/successfulAuthentication",type:t.from.type})})):t.mistake=e.data.msg})}}}),c=r,s=(i("4650"),i("2877")),f=Object(s["a"])(c,n,a,!1,null,null,null);e["default"]=f.exports},"268f":function(t,e,i){t.exports=i("fde4")},"32a6":function(t,e,i){var n=i("241e"),a=i("c3a1");i("ce7e")("keys",function(){return function(t){return a(n(t))}})},"454f":function(t,e,i){i("46a7");var n=i("584a").Object;t.exports=function(t,e,i){return n.defineProperty(t,e,i)}},4650:function(t,e,i){"use strict";var n=i("5d85"),a=i.n(n);a.a},"46a7":function(t,e,i){var n=i("63b6");n(n.S+n.F*!i("8e60"),"Object",{defineProperty:i("d9f6").f})},"5d85":function(t,e,i){},"85f2":function(t,e,i){t.exports=i("454f")},"8aae":function(t,e,i){i("32a6"),t.exports=i("584a").Object.keys},a4bb:function(t,e,i){t.exports=i("8aae")},bf90:function(t,e,i){var n=i("36c3"),a=i("bf0b").f;i("ce7e")("getOwnPropertyDescriptor",function(){return function(t,e){return a(n(t),e)}})},ce7e:function(t,e,i){var n=i("63b6"),a=i("584a"),o=i("294c");t.exports=function(t,e){var i=(a.Object||{})[t]||Object[t],r={};r[t]=e(i),n(n.S+n.F*o(function(){i(1)}),"Object",r)}},cebc:function(t,e,i){"use strict";var n=i("268f"),a=i.n(n),o=i("e265"),r=i.n(o),c=i("a4bb"),s=i.n(c),f=i("85f2"),u=i.n(f);function l(t,e,i){return e in t?u()(t,e,{value:i,enumerable:!0,configurable:!0,writable:!0}):t[e]=i,t}function m(t){for(var e=1;e<arguments.length;e++){var i=null!=arguments[e]?arguments[e]:{},n=s()(i);"function"===typeof r.a&&(n=n.concat(r()(i).filter(function(t){return a()(i,t).enumerable}))),n.forEach(function(e){l(t,e,i[e])})}return t}i.d(e,"a",function(){return m})},e265:function(t,e,i){t.exports=i("ed33")},ed33:function(t,e,i){i("014b"),t.exports=i("584a").Object.getOwnPropertySymbols},fde4:function(t,e,i){i("bf90");var n=i("584a").Object;t.exports=function(t,e){return n.getOwnPropertyDescriptor(t,e)}}}]);