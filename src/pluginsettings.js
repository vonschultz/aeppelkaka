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
import _ from 'lodash'
import { apiCallWithErrorHandling } from './api_utils.js'

function pluginCheckboxChanged (event) {
  for (
    const checkbox of document.querySelectorAll(
      'input[type="checkbox"].enable.plugin'
    )
  ) {
    const classes = _.difference(checkbox.classList, ['enable'])
    for (
      const input of document.querySelectorAll(
        ['input', ...classes].join('.') + ':not(.enable)'
      )
    ) {
      input.disabled = !checkbox.checked
    }
  }
}

async function pluginSettingsSubmit (event) {
  event.preventDefault()
  const data = new FormData(event.target)
  console.log(data, Object.fromEntries(data))
  const plugins = []
  for (const x of data.keys()) {
    if (x.startsWith('enable_')) {
      plugins.push(x.substring('enable_'.length))
    }
  }
  console.log(plugins)
  const pluginsettings = {}
  for (const plugin of plugins) {
    pluginsettings[plugin] = {}
    for (const x of data.keys()) {
      if (x.startsWith(plugin + '_')) {
        let value = data.get(x)
        const input = document.getElementById(x)
        if (input.type === 'number') {
          if (input.validity.valid) {
            value = input.valueAsNumber
          } else {
            throw Error(input.validity.validationMessage)
          }
        }
        pluginsettings[plugin][x.substring(plugin.length + 1)] = value
      }
    }
  }
  console.log(pluginsettings)
  const apiUrl = 'pluginsettings.json'
  const requestOptions = {
    method: 'PUT',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify(pluginsettings)
  }
  const response = await apiCallWithErrorHandling(apiUrl, requestOptions)
  console.log(response)
}

function registerEventHandlers () {
  const pluginSettings = document.getElementById('pluginSettings')
  if (pluginSettings) {
    pluginSettings.addEventListener('submit', pluginSettingsSubmit)

    for (
      const checkbox of document.querySelectorAll(
        'input[type="checkbox"].enable.plugin'
      )
    ) {
      checkbox.addEventListener('change', pluginCheckboxChanged)
    }
    pluginCheckboxChanged(null)
  }
}

$(registerEventHandlers)
