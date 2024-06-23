{*  Aeppelkaka, a program which can help a stundent learning facts.
 *  Copyright (C) 2021, 2023, 2024 Christian von Schultz
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
{extends file="layout.tpl"}
{block name=title}Æ: {$l['page title %s']|sprintf:$lesson_name}{/block}
{block name=body}

<h1>{$l['lesson %s']|sprintf:$lesson_name}</h1>

<table>
  <tr>
    <td id="num">{$l['number of cards:']}</td>
    <td headers="num">{$number_of_cards}</td>
  </tr>
  <tr>
    <td id="new">{$l['number of new cards:']}</td>
    <td headers="new">{$number_of_new_cards}</td>
  </tr>
  <tr>
    <td id="exp">{$l['number of expired cards:']}</td>
    <td headers="exp">{$number_of_expired_cards}</td>
  </tr>
  <tr>
    <td id="learned">{$l['number of learned cards:']}</td>
    <td headers="learned">{$number_of_learned_cards}</td>
  </tr>
{if $number_of_new_tomorrow_cards > 0}
  <tr>
    <td id="tomorrow">{$l['number of new tomorrow cards:']}</td>
    <td headers="tomorrow">{$number_of_new_tomorrow_cards}</td>
  </tr>
{/if}
</table>

<p>{$l['list label']}</p>
<ul>
<li>{$l['Add card (%url)']|sprintf:$url.lesson.addcard nofilter}</li>
{if $newexist}
<li>{$l['Learn new (%url)']|sprintf:$url.lesson.learncard nofilter}</li>
<li>{$l['Park new (%url)']|sprintf:$url.lesson.parknew nofilter}</li>
{/if}
{if $number_of_new_tomorrow_cards > 0}
<li>{$l['Unpark new (%url)']|sprintf:$url.lesson.unparknew nofilter}</li>
{/if}
{if $expiredexist}
<li>{$l['Test expired (%url)']|sprintf:$url.lesson.testexpired nofilter}</li>
{/if}
{if $tomorrowcardsexist && !$expiredexist}
<li>{$l['Test newly learnt (%url)']|sprintf:$url.lesson.newlylearnt nofilter}</li>
{/if}
{if $cardsexist}
<li>{$l['Remove a card (%url)']|sprintf:$url.lesson.removecard nofilter}</li>
{/if}
{if $expiredexist or $learnedexist}
<li>{$l['See graph (%url)']|sprintf:$url.lesson.graph nofilter}</li>
{/if}
{if $forgetstatsready}
<li>{$l['See forget percentage (%url)']|sprintf:$url.lesson.forgetstats nofilter}</li>
{/if}
<li>{$l['Change lesson properties (%url)']|sprintf:$url.lesson.properties nofilter}</li>
{foreach $sharing as $x}
<li><code>{$x.username}</code> {$l['is sharing with you.']}
  <a href="{$url.lesson.import_from_lesson}{$x.from_lesson_id}">{$l['Import cards']}</a></li>
{/foreach}
</ul>

{/block}
