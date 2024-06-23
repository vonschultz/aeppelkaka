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
export function showCardback () {
  document.getElementById('cardback').style.setProperty('display', 'block', null)

  if (document.getElementById('question')) {
    document.getElementById('question').style.setProperty('display', 'block', null)
  } else {
    if (document.getElementById('testinput')) {
      const input = new String(document.getElementById('testinput').value)
      const regex = new String(document.getElementById('regex').value)

      if (input.match(regex)) {
        document.getElementById('goodquestion').style.setProperty('display', 'block', null)
        document.getElementById('badquestion').style.setProperty('display', 'none', null)
        document.getElementById('wasgood').style.setProperty('display', 'none', null)
      } else {
        document.getElementById('badquestion').style.setProperty('display', 'block', null)
        document.getElementById('goodquestion').style.setProperty('display', 'none', null)
        document.getElementById('wasbad').style.setProperty('display', 'none', null)
      }

      if (document.getElementById('hiddenimage')) {
        document.getElementById('hiddenimage').style.setProperty('display', 'inline', null)
      }
    }
  }

  document.getElementById('cardback').scrollIntoView()
}
