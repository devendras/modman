Ghirardelli Front-end Buildkit
===================

Configuration
-------------

### Requires

- Ruby >= 1.9.3
- Ruby Gems
- NodeJS >= 0.10.0 (for Node Package Manager / NPM)
- Bower (for package management)


## INSTALL

`$ bundle install`

`$ npm install`

`$ bower install`


Development
--------

`$ grunt`

Your development files will be loacted in the `/source/` folder


### Fonts & Typography

Fonts leverage Sass' `@extend` directive using silent placeholders. Example: `%class-name`

This is done to avoid conflicts in the CSS cascade during compile, as opposed to extending a typical class such as `.class-name`


Sprites
--------

To support high pixel density devices (aka "Retina"), we need to save 2 versions of each image:

- 1x version
- 2x version

For example, our image `logo.png` is `250px x 100px` in our Photoshop PSD comp.

We will save out two versions of `logo.png` to create 2 separate sprites:

- 1x version: `250px x 100px` and saved in `/source/assets/img/sprites/`
- 2x version: `500px x 200px` and saved in `/source/assets/img/sprites-2x/`

Be sure to scale *down* your images. Do not take an image that is originally `250px x 100px` and scale it up. It will be distorted. Start with the "Retina" version first, and scale it down to @1x.

Images must be named the same in both folders. Using the example above, the file will be saved as:

- `/source/assets/img/sprites/logo.png`
- `/source/assets/img/sprites-2x/logo.png`

Sprite images can be refrenced by creating classes in the `_icons.scss` partial located in `/source/assets/scss/partials/styleguide/_icons.scss/`:

```
.icon__logo-class-name{
  @include sprite-retina(logo); // name of image, less file extension (exp: '.png')
}
```

Expand/Collapse
--------

Expand/Collapse (accordian) style elements.
```
<div class="panel-group" data-controller="collapse">
  <div class="collapse__panel panel">
      <header class="collapse__header panel-heading">
          <h2 class="collapse__header__title">Header/Title Text</h2>
          <button class="btn-default collapse__toggle collapsed" data-toggle="collapse" data-target="#collapse__1"></button>
      </header>
      <article id="collapse__1" class="panel-collapse collapse">
          <div class="collapse__body">
              <p>Cras justo odio, dapibus ac facilisis in, egestas eget quam. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Vestibulum id ligula porta felis euismod semper.</p>
          </div>
      </article>
  </div>
</div>
```

Custom Select Menus
--------

Custom select menus are using Select2. See their [documentation](http://ivaynberg.github.io/select2/ "Select2 Documentation").


Touch Events
--------

Touch event (e.g. swiperight, doubletap...etc) are handled by [HammerJS](https://github.com/hammerjs/hammer.js/wiki/Getting-Started "Hammer JS Getting Started") + the jquery wrapper. Below is an from a backbone initialize method. 


```
initialize: function(){
  this.$el.find('.className').hammer().on("swipeleft", this.callback);
}
```


Internet Explorer
--------

### IE8

Modernizr is used to polyfill/shim new HTML5 elements such as `<main>`, `<section>`, etc.:

```
<script src="/assets/js/vendor/modernizr/modernizr.js"></script>
```

NMWatcher & Selectivizr are leveraged to provide IE8 with pseudo-class and advanced `nth-child` support.

These scripts are loaded ***before*** Respond.js:

```
<!--[if IE 8]>
  <script src="/assets/js/vendor/nmwatcher/nmwatcher.min.js"></script>
  <script src="/assets/js/vendor/selectivizr/selectivizr.js"></script>
<![endif]-->
```

Respond.js is called last to make IE8 responsive / recognize media query breakpoints:

```
<script src="/assets/js/vendor/respond/src/respond.js"></script>
```

IE8-specific CSS can be found in:

* `/source/assets/css/partials/design/_main-ie.scss` (Design styles)
* `/source/assets/css/partials/layout/_structure-ie.scss` (Layout styles)

Any IE-specific CSS placed in these files will overwrite any of the "main" CSS in the cascade, thus `!important` tags will rarely be needed.

### IE6 & IE7

IE6 and IE7 are served an extremely stripped-down, but readable, stylesheet:

```
<link rel="stylesheet" href="http://universal-ie6-css.googlecode.com/files/ie6.1.1.css" media="screen, projection">
```

Additionally, a message is displayed and a link to Browse Happy provided, alerting the user their browser is no longer supported (located in `/inc/header.html`):

```
<!--[if lt IE 8]>
    <p class="browsehappy">We noticed you are using an outdated browser we no longer support. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
<![endif]-->
```

No Media Query Stylesheet
--------

### Premise
When a non-responsive stylesheet for a site is needed, a no media query
stylesheet is available. <br />The basic premise is that the no-media stylesheet will:
* Select the appropriate grid setup for desktop-only.
* Filter and find <strong>only</strong> the style definitions within the media
queries in the sass files that are relevent to the no-media-query stylesheet.
* Output an alternative stylesheet to be used <strong>in place of</strong> the
main style sheet.

### Use case
A common scenario where this is useful is for sites that support IE8 and below. Since
none of these older browsers exist on mobile devices in addition to the lack of
true media query support, a flat, non-responsive site is sometimes the best
solution.

### Setup
Set the variable to true to enable the no media query setup
  * File location: `/source/assets/scss/main-no-media.css.scss`

```
$disable-responsive: true;
```

Modify the grid to the desired setup for the no-media stylesheet <br /><em>(the
default is set to be exactly like the default grid-setup with the only
difference being the amount of columns as the large breakpoint)</em>.
  * File location: `/source/assets/scss/layout/_breakpoints.scss`

```
@else if $disable-responsive == true {

  .grid-construct {
    $total-columns: 12;
    $column-width: stripAndPxToEms(55px);
    $gutter-width: stripAndPxToEms(20px); //space between columns
    $grid-padding: stripAndPxToEms(20px);
    $container-width: stripAndPxToEms(980px);

    @include container;

    // @include susy-grid-background;
  }
}
```

When writing sass, use the no-media query mixin bp() instead of breakpoint().

```
bp($breakpoint-lg) { color: $white; }
```
This mixing will allow specified media queries to compile into the no-media stylesheet
while allowing the entirety to compile for the main stylesheet.

You can modify the
mixin to change which media queries to compile on a project by project basis.

* File location: `/source/assets/scss/partials/global/mixins/_misc.scss`

```
@mixin bp($media) {
  @if ($disable-responsive == false) {
    @include breakpoint($media)
    {
      @content;
    }
  } @else if ($disable-responsive == true) {
    // Modify this conditional to match the media queries to be included
    // in the no-media stylesheet.
    @if (($media == $breakpoint-md) or ($media == $breakpoint-lg)) {
      @content;
    }
  }
}
```
Set a min-width style to all containers that would normally be dynamic width
based on viewport size.

* File location: `/source/assets/scss/partials/design/_no-media.scss`

```
.grid-construct { min-width: 980px; }
```





Production
--------

`$ grunt build`

Your production-ready app will be located in `/build/`

`$ grunt serve`

Use this to view your production build.
