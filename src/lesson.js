/*  Aeppelkaka, a program which can help a stundent learning facts.
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
 */

import $ from 'jquery'
import { apiCallWithErrorHandling } from './api_utils.js'

async function parking (newCards) {
  const requestOptions = {
    method: 'PUT',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({ new_cards: newCards })
  }
  const response = await apiCallWithErrorHandling(
    window.aeppelkakaUrls.lesson['parking.json'],
    requestOptions
  )
  console.log(response)
  if (response.success) {
    window.location.reload()
  }
}

async function parkNew (event) {
  await parking(parseInt(event.target.value))
}

async function unpark (event) {
  await parking(parseInt(event.target.max) - parseInt(event.target.value))
}

$(document).ready(function () {
  const newcards = document.getElementById('number_of_new_cards')
  const tomorrow = document.getElementById('number_of_new_tomorrow_cards')
  if (newcards) {
    newcards.addEventListener('change', parkNew)
  }
  if (tomorrow) {
    tomorrow.addEventListener('change', unpark)
  }
})
