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

/* global Event, MutationObserver */

import './aeppelchess.css'

import $ from 'jquery'
import _ from 'lodash'
import { pgnView, pgnEdit } from '@mliebelt/pgn-viewer'

let $cardfront = null
let $cardback = null
let $testinput = $('#testinput')

function isMobile () { return ('ontouchstart' in document.documentElement) }

function togglemenu () {
  const menu = document.getElementById('menu')
  if (menu.style.display === 'block') {
    menu.style.display = 'none'
  } else {
    menu.style.display = 'block'
  }
}

function nagsEqual (nag1, nag2) {
  if (nag1 === null || nag1 === undefined) {
    nag1 = []
  }
  if (nag2 === null || nag2 === undefined) {
    nag2 = []
  }
  return _.isEqual(nag1, nag2)
}

function compareFrontAndBack (cardId, pgnview, pgnedit) {
  const chesspgnedit = 'card_' + cardId + '_chesspgnedit'
  const chesspgnview = 'card_' + cardId + '_chesspgnview'
  const backsideMoves = pgnview.base.getPgn().getMoves()
  const frontsideMoves = pgnedit.base.getPgn().getMoves()
  const wrongPiece = '1px solid red'
  const wrongSquare = '1px solid orange'
  const wrongNag = '1px solid brown'
  const correct = '0px solid white'
  for (
    let i = 0, j = 0;
    i < backsideMoves.length && j < frontsideMoves.length;
    i++, j++
  ) {
    const viewmove = document.getElementById(chesspgnview + 'Moves' + i)
    while (!frontsideMoves[j] && j < frontsideMoves.length) {
      j++
    }
    if (j === frontsideMoves.length) {
      break
    }
    if (backsideMoves[i].from !== frontsideMoves[j].from) {
      viewmove.style.outline = wrongPiece
      if (viewmove.checkVisibility()) {
        document.getElementById(chesspgnedit + 'Moves' + j).style.outline = wrongPiece
      }
    } else if (backsideMoves[i].to !== frontsideMoves[j].to) {
      viewmove.style.outline = wrongSquare
      if (viewmove.checkVisibility()) {
        document.getElementById(chesspgnedit + 'Moves' + j).style.outline = wrongSquare
      }
    } else if (!nagsEqual(backsideMoves[i].nag, frontsideMoves[j].nag)) {
      viewmove.style.outline = wrongNag
      if (viewmove.checkVisibility()) {
        document.getElementById(chesspgnedit + 'Moves' + j).style.outline = wrongNag
      }
    } else {
      viewmove.style.outline = correct
      if (viewmove.checkVisibility()) {
        document.getElementById(chesspgnedit + 'Moves' + j).style.outline = correct
      }
    }
  }
}

function aeppelchessRunCard (cardId) {
  $testinput = $('#testinput_' + cardId)
  $cardfront = $('#cardfront_' + cardId)
  $cardback = $('#cardback_' + cardId)

  const chesspgnedit = 'card_' + cardId + '_chesspgnedit'
  const chesspgnview = 'card_' + cardId + '_chesspgnview'

  $testinput.blur()

  $('h1').click(togglemenu)
  if (isMobile()) {
    const menu = document.getElementById('menu')
    menu.style.display = 'none'
  }

  $cardfront.append(
    '<div id="' + chesspgnedit + '" style="width: 300px"></div>'
  )
  const pgnconfig = {
    showCoords: false,
    theme: 'brown',
    pieceStyle: 'wikipedia',
    showFen: false
  }
  const pgnedit = pgnEdit(chesspgnedit, { ...pgnconfig, timerTime: 1400 })
  $cardback.append('<div id="' + chesspgnview + '" style="width: 300px"></div>')
  pgnconfig.pgn = $cardback.text()
  const pgnview = pgnView(chesspgnview, pgnconfig)

  // Base the regex on the pgnview, but allow extra spaces around
  // comments, as the pgnedit widget likes to add that. Also allow
  // spaces and an extra * at the end, which might not be present
  // in the pgnview, but will be added by pgnedit.
  if (document.getElementById('regex')) {
    document.getElementById('regex').value = (
      '^' +
      _.escapeRegExp(
        pgnview.base.getPgn().writePgn()
      ).replace(/\\[}{]/g, '\\s*$&\\s*') +
            '\\s*\\*?\\s*$')
  }

  $('#cardback_' + cardId).on('showCardback', () => {
    compareFrontAndBack(cardId, pgnview, pgnedit)
  })

  const observer = new MutationObserver((mutations) => {
    mutations.forEach((mutation) => {
      $testinput.val(pgnedit.base.getPgn().writePgn())
      $testinput.blur()
      compareFrontAndBack(cardId, pgnview, pgnedit)
      const madeMoves = pgnedit.base.getPgn().getMoves()
      const allMoves = pgnview.base.getPgn().getMoves()
      const nextMove = allMoves[madeMoves.length]
      if (nextMove && nextMove.commentAfter === 'given') {
        pgnedit.base.onSnapEnd(nextMove.from, nextMove.to)
        pgnedit.board.set({ fen: pgnedit.base.chess.fen() })
        $('#' + chesspgnedit + 'Buttonprev').click()
        $('#' + chesspgnedit + 'Buttonnext').click()
        document.querySelector('#comment' + chesspgnedit + 'Button input.afterComment').click()
        document.querySelector('#comment' + chesspgnedit + 'Button textarea').value = 'given'
        document.querySelector('#comment' + chesspgnedit + 'Button textarea').dispatchEvent(
          new Event('change'))
      }
    })
  })

  observer.observe(document.getElementById(chesspgnedit + 'Moves'), {
    characterDataOldValue: true,
    subtree: true,
    childList: true,
    characterData: true
  })
}

export function aeppelchessRun () {
  if (document.getElementById('menu').textContent.match(/Chess/i)) {
    for (const element of document.querySelectorAll('.cardback')) {
      try {
        aeppelchessRunCard(element.id.replace('cardback_', ''))
      } catch (error) {
        console.error(error)
      }
    }
  } else {
    console.log('Chess content disabled on this page.')
  }
}
