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
{block name=title}Æ: {$l['page title: users']}{/block}
{block name=body}
<p>{$l['Users:']}</p>

<table class="main" id="userlist">
{foreach $users as $user}
  {if $user@first}
  <tr>
    <th class="text" id="userlist_unamecol">{$l['Username']}</th>
    <th class="text" id="userlist_namecol">{$l['Full name']}</th>
    <th class="numbers" id="userlist_lessonnumcol">{$l['Number of lessons']}</th>
    <th class="numbers" id="userlist_cardnumcol">{$l['Number of cards']}</th>
    <th class="date" id="userlist_sessionstartcol">{$l['Session start']}</th>
    <th class="date" id="userlist_lastactivecol">{$l['Last activity']}</th>
  </tr>
  {/if}
  <tr>
    <td class="text" headers="userlist_unamecol"><a href="users?user={$user->user_id|escape:'url'}">{$user->username|default:'—'}</a></td>
    <td class="text" headers="userlist_namecol">{$user->full_name|default:'—'}</td>
    <td class="numbers" headers="userlist_lessonnumcol">{$user->number_of_lessons|default:'unknown'}</td>
    <td class="numbers" headers="userlist_cardnumcol">{$user->number_of_cards|default:'unknown'}</td>
    <td class="date" headers="userlist_sessionstartcol">{$user->session_start|date_format:'%H:%M:%S'}</td>
    <td class="date" headers="userlist_lastactivecol">{$user->session_last_active|date_format:'%H:%M:%S'}</td>
  </tr>
  {if $user@last}
  <tr>
    <td colspan="2"><strong>{$l['Number of users: %s']|sprintf:$user@total}</strong></td>
    <td class="numbers"><strong>{$total_number_of_lessons|default:'unknown'}</strong></td>
    <td class="numbers"><strong>{$total_number_of_cards|default:'unknown'}</strong></td>
  </tr>
  {/if}
{foreachelse}
  <tr>
    <td><strong>{$l['No users in system']}</strong></td>
  </tr>
{/foreach}
</table>

{if !empty($user_id)}
<form action="{$url.login}?hijack={$user_id|escape:'url'}"
      method="post" accept-charset="UTF-8">
<p><input type="submit" value="{$l['hijack user']}" /></p>
</form>
{/if}

{if isset($display_user_lessons)}
{include file='lessonlist.tpl'}
{/if}
{/block}
