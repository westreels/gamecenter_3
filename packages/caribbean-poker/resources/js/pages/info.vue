<template>
  <div>
    <h5>
      {{ $t('How to play') }}
    </h5>
    <p>
      {{ $t('Caribbean Stud Poker is a variation of the traditional American Five-Card Stud Poker.') }}
      {{ $t('Players bet against the house instead of each other.') }}
    </p>
    <p>
      {{ $t('To start the game and receive your cards you must make an opening bet called Ante.') }}
      {{ $t('Once you make your bet, you will receive five cards.') }}
      {{ $t('The dealer will also receive five cards.') }}
      {{ $t('Then the dealer will flip over one hole card, which you can look at and use to decide what to do with your hand.') }}
    </p>
    <p>
      {{ $t('You can either fold or call.') }}
      {{ $t('If you fold, you lose the hand and automatically forfeit your ante bet.') }}
      {{ $t('If you call, you must place an extra wager equal to twice the amount of the ante bet.') }}
    </p>
    <p>
      {{ $t('After that the dealer will flip over his remaining four cards and the dealer hand is compared to your hand.') }}
      {{ $t('The dealer hand only qualifies if his hand either contains both an ace and a king or forms a pair or any higher-ranked poker hand.') }}
      {{ $t('If the dealer does not qualify you will win even money on the ante wager and the call bet will push.') }}
      {{ $t('If the dealer qualifies and beats your hand, both ante and raise will lose.') }}
      {{ $t('If the dealer qualifies and loses to your hand, then the ante will pay even money and the call bet according to the paytable.') }}
      {{ $t('If the player and dealer tie, both ante and raise will push.') }}
    </p>
  </div>
</template>
