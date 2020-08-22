# yt-video-embedder
Wordpress plugin. You can easily embed videos from Youtube etc. responsively.

You can either embed a single video, or a group of videos stored in a custom database table. Both shortcodes and widgets are available.

Just use the Youtube video link copied from your browser like this: `https://www.youtube.com/watch?v=BHACKCNDMW8`

You don't need to click on Share and get your embed code on Youtube. This plugin automatically generates embed code.

Options available:
- Enhanced-privacy mode (`https://www.youtube-nocookie.com`)
- Autoplay (`&autoplay=1`)
- Start video at sertain time in seconds (`?start=90`)
- Enable captions (`&cc_load_policy=1`)


## Install

Unzip it into your Wordpress plugins directory.

## Usage

### Single video shortcode

- `[single_video_embed]`

- `[single_video_embed url="" title="" start_time="" heading_level="" font_weight="" privacy="" autoplay="" captions=""]`

Default arg values:

  'url'             => self::DEFAULT_LINK,
  'title'           => __('This is a default video, add your url in shortcode'),
  'start_time'      => 0,
  'heading_level'   => 'h4',
  'font_weight'     => '700',
  'privacy'         => 'off',
  'autoplay'        => 'off',
  'captions'        => 'off'

Font weight is for the heading title.


### Group video shortcode


[group_video_embed heading="" heading_level="" font_weight="" ]

Default arg values:

  'heading'        => __('This is a default video, add your url in shortcode'),
  'heading_level'  => 'h3',
  'font_weight'    => '700',
  'limit'          => 0, *
  'order_by'       => 'date', // title *
  'order'          => 'DESC', // ASC *

* These args are not implemented yet !


### Single Youtube Video Embed widget

Look for the widget called **Single Youtube Video Embed**

Set these options:
- title (default: '')
- url (default: DEFAULT_LINK)
- start time (default: 0)
- privacy-enhanced mode (default: disabled)
- autoplay (default: disabled)
- captions (default: disabled)


### Youtube Video Group Embed widget

1. Select "Add new" menu option / "Add new video" button to store your videos in the database.
2. Select **Youtube Video Group Embed**

You can only set the video group title argument
- title (default: '')


## TODO list

- Youtube playlists are currently not supported !
- add `limit`, `order`, `order_by` options for group shortcode/widget !
- set captions in a certain language like `&cc_lang_pref=fr&cc_load_policy=1` which is French !

