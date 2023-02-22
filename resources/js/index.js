import "../css/styles.scss"
// Templates
// style
import.meta.glob("../../public/themes/wordplate/layouts/**/style.scss", {eager: true})
// script
import.meta.glob("../../public/themes/wordplate/layouts/**/script.js", {eager: true})
//images
import.meta.glob('../static/img/**/*.{png,jpg,jpeg,gif,svg}', {eager: true})
//fonts
import.meta.glob('../static/fonts/**/*.{woff,woff2,ttf,eot}', {eager: true})
