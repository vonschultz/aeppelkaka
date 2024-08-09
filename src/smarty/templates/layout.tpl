{*  Aeppelkaka, a program which can help a stundent learning facts.
 *  Copyright (C) 2021, 2022, 2024 Christian von Schultz
 *
 *  Permission is hereby granted, free of charge, to any person
 *  obtaining a copy of this software and associated documentation
 *  files (the “Software”), to deal in the Software without
 *  restriction, including without limitation the rights to use, copy,
 *  modify, merge, publish, distribute, sublicense, and/or sell copies
 *  of the Software, and to permit persons to whom the Software is
 *  furnished to do so, subject to the following conditions:
 *
 *  The above copyright notice and this permission notice shall be
 *  included in all copies or substantial portions of the Software.
 *
 *  THE SOFTWARE IS PROVIDED “AS IS”, WITHOUT WARRANTY OF ANY KIND,
 *  EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
 *  MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 *  NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS
 *  BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN
 *  ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN
 *  CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 *  SOFTWARE.
 *
 * SPDX-License-Identifier: MIT
 *}
{function print_card backvisible=1}
<p class="cardfronttitle">{$l['Front']}</p>
<div class="cardfront"><p>{$cardfront nofilter}</p></div>
<p class="cardbacktitle">{$l['Back']}</p>
<div class="cardback{if !$backvisible} black{/if}">
<p>{$cardback nofilter}</p></div>
{if !$backvisible}
<p><input id="testinput{$card_id}" type="text"
style="width: 100%; font-family: monospace; font-size: smaller"/>
</p>
{/if}
{/function}{* print_card *}
{assign var='title' value=$title|default:'Aeppelkaka'}
{assign var='relative_url' value=$relative_url|default:''}
{assign var='focus_element' value=$focus_element|default:''}
{assign var='body' value=$body|default:''}
<!DOCTYPE html>
<html lang="{$lang|default:'en'}">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, height=device-height, user-scalable=no, initial-scale=1, target-densitydpi=device-dpi, maximum-scale=1.0" />
  <base href="{$webdir}/{$relative_url|escape}"/>
  <link href="{$webdir}/{$manifest['main.css']}" rel="stylesheet" type="text/css"/>
  <script type="text/javascript" src="{$webdir}/{$manifest['main.js']}"></script>
  <title>{block name=title}{$title}{/block}</title>
  {block name=head}{/block}
  
  <!-- We use Yahoo YUI 2 menu bar. -->
  {strip}
  <link
    rel="stylesheet"
    type="text/css"
    href="{$yuidir}/container/assets/skins/sam/container.css"
  />{/strip}
  {strip}
  <link
    rel="stylesheet"
    type="text/css"
    href="{$yuidir}/menu/assets/skins/sam/menu.css"
  />{/strip}
  {strip}
  <script
    type="text/javascript"
    src="{$yuidir}/utilities/utilities.js">
  </script>{/strip}
  {strip}
  <script
    type="text/javascript"
    src="{$yuidir}/container/container.js">
  </script>{/strip}
  {strip}
  <script
    type="text/javascript"
    src="{$yuidir}/menu/menu.js">
  </script>{/strip}

  <script type="text/javascript">
//<![CDATA[

{function mitem enabled=1 indent=10}{capture name="mitem"}
{ldelim}
  text: "{$thetext|escape:'javascript'}",
{if $url.this == $theurl || !$enabled}
  disabled: true
{else}
  url: "{$theurl|escape:'javascript'}"
{/if}
{rdelim}{/capture}
{$smarty.capture.mitem|indent:$indent nofilter}{/function}

var theItemData = [
{if !empty($card_id)}
  {ldelim}
    text: "{$l['m:Card']|escape:'javascript'}",
    submenu:
    {ldelim}
      id: "cardmenu",
      itemdata: [
{call mitem thetext=$l['m:Remove this card']
            theurl=$url.card.removecard
            indent=8}
      ]
    {rdelim}
  {rdelim},
{/if}
{if !empty($lesson_id)}
  {ldelim}
    text: "{$l['m:Lesson']|escape:'javascript'}",
    submenu:
    {ldelim}
      id: "lessonmenu",
      itemdata: [
        [
{call mitem thetext=$l['m:New card']
            theurl=$url.lesson.addcard},
{call mitem thetext=$l['m:Remove card']
            theurl=$url.lesson.removecard
            enabled=$cardsexist}
        ],
        [
{call mitem thetext=$l['m:Learn new']
            theurl=$url.lesson.learncard
            enabled=$newexist},
{call mitem thetext=$l['m:Test expired']
            theurl=$url.lesson.testexpired
            enabled=$expiredexist},
{call mitem thetext=$l['m:Test newly learnt']
            theurl=$url.lesson.newlylearnt
            enabled=($tomorrowcardsexist && !$expiredexist && !$newexist)}
        ],
        [
{call mitem thetext=$l['m:See graph']
            theurl=$url.lesson.graph
            enabled=($expiredexist or $learnedexist)},
{call mitem thetext=$l['m:Forget percentage']
            theurl=$url.lesson.forgetstats
            enabled=$forgetstatsready}
        ],
        [
{call mitem thetext=$l['m:Lesson properties']
            theurl=$url.lesson.properties}
        ],
        [
          {ldelim}
            text: "{$l['m:Lesson Debug']|escape:'javascript'}",
            submenu:
            {ldelim}
              id: "lessondebug",
              itemdata: [
{call mitem thetext=$l['m:List cardbacks']
            theurl=$url.lesson.listcardbacks
            enabled=$cardsexist
            indent=16}
              ]
            {rdelim}
          {rdelim}
        ]
      ]
    {rdelim}
  {rdelim},
{/if}
{if !empty($l['m:System'])}
  {ldelim}
    text: "{$l['m:System']|escape:'javascript'}",
    submenu:
    {ldelim}
      id: "systemmenu",
      itemdata: [
        [
{call mitem thetext=$l['m:Lessonlist'] theurl=$url.listoflessons},
{call mitem thetext=$l['m:Setup']      theurl=$url.setup},
        ],
        [
{call mitem thetext=$l['m:Logout']     theurl=$url.logout}
        ]
      ]
    {rdelim}
  {rdelim},
{/if}
{if !empty($l['m:Help'])}
  {ldelim}
    text: "{$l['m:Help']|escape:'javascript'}",
    submenu:
    {ldelim}
      id: "helpmenu",
      itemdata: [
{call mitem thetext=$l['m:Manual']     theurl=$url.help  indent=8},
      ]
    {rdelim}
  {rdelim}
{/if}
];

YAHOO.util.Event.onDOMReady(function () {ldelim}
  /*
     Instantiate a MenuBar:  The first argument passed to the 
     constructor is the id of the element to be created; the 
     second is an object literal of configuration properties.
  */
  if (theItemData.length > 0) {ldelim}
    var theMenuBar = new YAHOO.widget.MenuBar(
      "aemenubar",
      {ldelim}
        lazyload: true,
        itemdata: theItemData
      {rdelim}
    );
    theMenuBar.render(document.body);
  {rdelim}
{if $focus_element}
  document.getElementById("{$focus_element|escape}").focus();
{/if}
{rdelim});

//]]>
  </script>
</head>

<body class="yui-skin-sam">

<div id="content">
{function menuitem}{strip}
<td class="menuitem">
  {if $url.this == $theurl}
    <strong class="nolink" title="{$thetitle}">{$thetext}</strong>
  {else}
    <a class="menulink" href="{$theurl}" title="{$thetitle}">
      {$thetext}
    </a>
  {/if}
</td>
{/strip}{/function}

{if !empty($l['Logout'])}
<table id="menu">
{if $lesson_name}
  <tr class="menuline">
    {call menuitem
          theurl=$url.thislesson
          thetitle=$l['lesson %s']|sprintf:$lesson_name
          thetext=$lesson_name}
  </tr>
{/if}
  <tr class="menuline">
    {call menuitem
          theurl=$url.listoflessons
          thetitle=$l['Main page with lessons']
          thetext=$l['Lessons']}
  </tr>
  <tr class="menuline">
    {call menuitem
          theurl=$url.setup
          thetitle=$l['Aeppelkaka settings']
          thetext=$l['Setup']}
  </tr>
  <tr class="menuline">
    {call menuitem
          theurl=$url.help
          thetitle=$l['The Aeppelkaka manual']
          thetext=$l['Help']}
  </tr>
  <tr class="menuline">
    {call menuitem
          theurl=$url.logout
          thetitle=$l['Logout of Aeppelkaka']
          thetext=$l['Logout']}
  </tr>
{if $isadmin}
  <tr class="menuline">
    {call menuitem
          theurl=$url.userlist
          thetitle=$l['Administrator: view users']
          thetext=$l['Show users']}
  </tr>
{/if}
</table>
{/if}

{block name=body}{$body nofilter}{/block}

</div>

</body>

</html>
