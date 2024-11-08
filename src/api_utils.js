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

async function htmlApiErrorDialog (status, statusText, body) {
  const dialog = document.createElement('dialog')
  const dialogForm = document.createElement('form')
  dialogForm.setAttribute('method', 'dialog')
  const dialogStatusDiv = document.createElement('div')
  const dialogStatusP = document.createElement('p')
  dialogStatusP.innerText = status + ' ' + statusText
  dialogStatusDiv.append(dialogStatusP)
  dialogForm.append(dialogStatusDiv)
  const dialogDiv = document.createElement('div')
  const dialogDivShadowRoot = dialogDiv.attachShadow({ mode: 'open' })
  dialogDivShadowRoot.innerHTML = body
  dialogForm.append(dialogDiv)
  const dialogButtonDiv = document.createElement('div')
  const dialogButton = document.createElement('button')
  dialogButton.innerText = 'OK'
  dialogButtonDiv.append(dialogButton)
  dialogForm.append(dialogButtonDiv)
  dialog.append(dialogForm)
  document.querySelector('body').append(dialog)
  dialog.showModal()
  return dialog
}

async function jsonApiErrorDialog (status, statusText, body) {
  window.alert(status + ' ' + statusText + '\n' + body)
}

function responseIsJson (response) {
  return response.headers.get('Content-Type') === 'application/json'
}

export async function apiCallWithErrorHandling (apiUrl, requestOptions) {
  const response = await window.fetch(apiUrl, requestOptions)
  if (response.ok && responseIsJson(response)) {
    const responseData = await response.json()
    return responseData
  } else if (responseIsJson(response)) {
    const responseData = await response.json()
    console.error('Error in fetch of ' + apiUrl, responseData)
    jsonApiErrorDialog(
      response.status,
      response.statusText,
      responseData.error
    )
    return null
  } else {
    const responseData = await response.text()
    htmlApiErrorDialog(
      response.status,
      response.statusText,
      responseData
    )
    return null
  }
}
