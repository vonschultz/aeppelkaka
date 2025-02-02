/*  Aeppelkaka, a program which can help a stundent learning facts.
 *  Copyright (C) 2024 Veronika von Schultz
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

import './texttospeech.css'

function setSpeech () {
  return new Promise(
    (resolve, reject) => {
      const interval = setInterval(() => {
        if (window.speechSynthesis.getVoices().length > 0) {
          resolve()
          clearInterval(interval)
        }
      }, 15)
    }
  )
}

export function readCardBack (cardId) {
  const promiseVoices = setSpeech()
  promiseVoices.then(() => {
    const settings = window.texttospeechSettings
    const synth = window.speechSynthesis
    const voices = synth.getVoices()
    const cardBack = document.getElementById('cardback_' + cardId).textContent

    const speech = new SpeechSynthesisUtterance()

    let filteredVoices = []

    for (const voiceURI in settings.voiceURIs) {
      filteredVoices = voices.filter((v) => {
        return v.voiceURI === voiceURI
      })
      if (filteredVoices.length > 0) {
        break
      }
    }

    if (filteredVoices.length === 0) {
      filteredVoices = voices.filter(function (v) {
        return v.lang === settings.lang
      })
    }

    if (filteredVoices.length === 0) {
      window.alert('No voice found for text-to-speech.')
      return
    }

    speech.voice = filteredVoices[0]
    speech.lang = window.texttospeechSettings.lang
    speech.text = cardBack
    speech.rate = 1
    speech.pitch = 1
    speech.volume = 1

    synth.speak(speech)
  })
}

export function texttospeechRun (settings) {
  window.texttospeechSettings = settings
  setSpeech()
  for (const cardbacktitle of document.querySelectorAll('.cardbacktitle')) {
    const cardId = cardbacktitle.id.replace('cardbacktitle_', '')
    const button = document.createElement('button')
    button.setAttribute('class', 'speakButton')
    button.textContent = 'Play the back side'
    cardbacktitle.appendChild(button)
    button.addEventListener(
      'click',
      (event) => {
        event.preventDefault()
        readCardBack(cardId)
      }
    )
  }
}
