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
{if isset($lessons)}
<table class="main" id="lessonlist">
{foreach $lessons as $lesson}
{if $lesson@first}
  <tr>
    <th id="lessonlist_lessonnamecol">{$l['Lesson']}</th>
    <th class="numbers" id="lessonlist_newcol">{$l['New']}</th>
    <th class="numbers" id="lessonlist_expiredcol">{$l['Expired']}</th>
    <th class="numbers" id="lessonlist_learnedcol">{$l['Learned']}</th>
    <th class="numbers" id="lessonlist_totalcol">{$l['Total']}</th>
  </tr>
{/if}
  <tr>
    <td headers="lessonlist_lessonnamecol">{strip}
      {if empty($lesson->url)}
        {$lesson->lesson_name|default:'?'}
      {else}
        <a href="{$lesson->url}">{$lesson->lesson_name|default:'?'}</a>
      {/if}
    {/strip}</td>
    <td headers="lessonlist_newcol" class="numbers">{$lesson->new|default:'?'}</td>
    <td headers="lessonlist_expiredcol" class="numbers">{$lesson->expired|default:'?'}</td>
    <td headers="lessonlist_learnedcol" class="numbers">{$lesson->learned|default:'?'}</td>
    <td headers="lessonlist_totalcol" class="numbers">{$lesson->total|default:'?'}</td>
  </tr>
{if $lesson@last}
  <tr>
    <td><strong>{$l['All in all']}</strong></td>
    <td headers="lessonlist_newcol" class="numbers"><strong>{$total_new|default:'?'}</strong></td>
    <td headers="lessonlist_expiredcol" class="numbers"><strong>{$total_expired|default:'?'}</strong></td>
    <td headers="lessonlist_learnedcol" class="numbers"><strong>{$total_learned|default:'?'}</strong></td>
    <td headers="lessonlist_totalcol" class="numbers"><strong>{$user_total_number_of_cards|default:'?'}</strong></td>
  </tr>
{/if}
{foreachelse}
  <tr>
    <td>{$l['No lessons']}</td>
  </tr>
{/foreach}
</table>
{else}
<p>{$l['No lessons']}</p>
{/if}
