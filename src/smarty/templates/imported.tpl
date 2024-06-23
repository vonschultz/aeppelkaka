{*  Aeppelkaka, a program which can help a stundent learning facts.
 *  Copyright (C) 2021, 2024 Christian von Schultz
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

<p>The following cards were imported:</p>

{foreach $cards as $card}
<h2>Card {$card.card_id}</h2>
{call print_card
      card_id=$card.card_id
      cardfront=$card.cardfront
      cardback=$card.cardback}
{foreachelse}
<p>No cards found</p>
{/foreach}

{if !empty($cards)}
<p>I suggest you now go and <a href="{$url.lesson.learncard}">learn
    the new cards</a>.</p>
{/if}


{/block}
