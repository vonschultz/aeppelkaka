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

<form action="{$url.this}" method="post" accept-charset="UTF-8">
<p class="inset cyan">Skip the first
<input type="text" name="offset"
       size="5" value="{$offset}"/> cards.
<input type="submit" /></p>
</form>

<form action="{$url.this}" method="post" accept-charset="UTF-8">
<p class="inset yellow">Search for cards with 
<input type="text" name="cardfrontsearch"
{if !empty($cardfrontsearch)} value="{$cardfrontsearch}"{/if}
/>
on the card front, and 
<input type="text" name="cardbacksearch"
{if !empty($cardbacksearch)} value="{$cardbacksearch}"{/if}
/>
on the card back.
<input type="submit" /></p>
</form>


<form action="{$url.this}" method="post" accept-charset="UTF-8">
<p>Select the cards you want to import below. Then click
<input type="submit" value="Import cards"/>
</p>
{foreach $cards as $card}
<h2>Card {$card.card_id}</h2>
<p><input type="checkbox" name="importcards[]" value="{$card.card_id}" />
Import the card below.</p>
{call print_card
      card_id=$card.card_id
      cardfront=$card.cardfront
      cardback=$card.cardback}
{foreachelse}
<p>No cards found</p>
{/foreach}
</form>


{/block}
