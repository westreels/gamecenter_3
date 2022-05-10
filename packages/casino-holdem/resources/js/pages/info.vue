<template>
  <div>
    <h5>
      {{ $t('How to play') }}
    </h5>
    <p>
      {{ $t('Casino Hold\'Em is a poker variation, similar to Texas Hold\'Em.') }}
      {{ $t('The main difference is that in Casino Hold\em you don\'t play with other players, but only against the dealer.') }}
    </p>
    <p>
      {{ $t('The Casino Hold\'em goal is to beat the Dealer\'s hand.') }}
      {{ $t('Casino Hold\'em starts with Ante bet.') }}
      {{ $t('The first step is to receive two face-up cards for you, two hidden – for the Dealer and three flop cards – again with faces up.') }}
      {{ $t('After receiving your two cards and the three "flop" cards, you have to evaluate your hand and choose among the following.') }}
    </p>
    <ul>
      <li>{{ $t('Fold – you lose your Ante bet and the game is over.') }}</li>
      <li>{{ $t('Call – you continue with an additional Call bet (equal to twice the Ante bet) and two extra community cards are dealt.') }}</li>
    </ul>
    <p />
    <p>
      {{ $t('When all the five flop cards are on the table, the Dealer\'s cards are turned over.') }}
      {{ $t('Your best hand is formed by a combination between your own two cards and the five flop cards.') }}
      {{ $t('The hands are compared to each other and the highest one wins the game.') }}
    </p>
    <p>
      {{ $t('You might make an additional bonus bet called AA bonus bet.') }}
      {{ $t('Bonus bet counts your two own cards and the three flop cards, dealt on the first round of the game.') }}
      {{ $t('This bet is not related to the other dealing results.') }}
      {{ $t('You might lose the game, but the bonus bet might win or vice versa.') }}
    </p>
    <p>
      {{ $t('Dealer qualifies only with a hand starting from Pair of 4 and higher.') }}
      {{ $t('If the Dealer does not qualify the Ante bet is paid according the Ante paytable and the Call bet is returned.') }}
      {{ $t('If the Dealer qualifies, and the player\'s hand is better than the dealer\'s, the Ante bet pays according to the Ante paytable and the Call bet pays 1 to 1.') }}
      {{ $t('If the dealer qualifies, and the dealer\'s hand is equal to the player\'s, all bets are push.') }}
      {{ $t('If the dealer qualifies, and the dealer\'s hand is better than the player\'s, the player loses all bets.') }}
    </p>
  </div>
</template>
