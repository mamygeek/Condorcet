<?php
declare(strict_types=1);

/* NO INTERFACE here, see html examples for it */

# Quick tour of the main features of Condorcet PHP

// I - Install
    use Condorcet\Condorcet;
    use Condorcet\Election;
    use Condorcet\Candidate;
    use Condorcet\Vote;

    require_once '../__CondorcetAutoload.php';

    $start_time = microtime(TRUE);

// II - Create Election

$myElection1 = new Election ();


// III - Manage Candidate

    # -A- Add candidates

        // A string
        $myElection1->addCandidate('Debussy');
        $myElection1->addCandidate('Caplet');
        $myElection1->addCandidate('Olivier Messiaen');
        $myElection1->addCandidate('Ligeti');
        $myElection1->addCandidate('A');

        // Automatic name
        $myAutomaticCandidate = $myElection1->addCandidate(); // return an automatic Condorcet\Candidate object named 'B';

        // An objet
        $myElection1->addCandidate( new Candidate ('Koechlin') );

        $myLutoCandidate = new Candidate ('Lutoslawski');
        $myElection1->addCandidate( $myLutoCandidate );


    # -B- Change your mind ?

        $myElection1->removeCandidate('A');
        $myElection1->removeCandidate($myAutomaticCandidate);

        // Lutoslawski change his name
        $myLutoCandidate->setName('Wiltod Lutoslawski'); # Done !

            # What was his old names?
            $myLutoCandidate->getHistory(); // return the full history with timestamp of this Candidate naming


    # -C- Check your candidate list, if you forget it
        $myElection1->getCandidatesList(); // Return an array pupulate by each Candidate objet
        $myElection1->getCandidatesList(true); // Return an array pupulate by each Candidate name as String.

        // OK, I need my Debussy (want his candidate object)
        $myDebussyCandidate = $myElection1->getCandidateObjectByName('Debussy');


// IV - Manage Votes

    # -A- Add Votes

        $VoteModel = [$myDebussyCandidate, 'Caplet', array('Olivier Messiaen', 'Ligeti'), 'Koechlin']; // Messiaen & Ligeti are at equallity. $myLutoCandidate will be automatically detect in $myElection1 objet

        for ($i = 0 ; $i < 95 ; $i++) {
            shuffle($VoteModel);
            $myElection1->addVote( $VoteModel );
        }

            // How Many Vote could I Have now ?
            $myElection1->countVotes(); // Return 95 (int)


        // More fun way to add Vote from full string input !
        $myVote96 = $myElection1->addVote('Debussy > Olivier Messiaen = Ligeti > Caplet');
        
        //Add some tags
        $myVote97 = $myElection1->addVote(
                                            array($myDebussyCandidate, 'Koechlin'),
                                            ['greatFrenchVote','strangeVote'] // You can also pu your tags for this vote
        );

        // Parse multiple Votes
        $myElection1->parseVotes("
            tag1,frenchies,tag3 || Olivier Messiaen > Debussy = Caplet > Ligeti # Tags at first, vote at second, separated by '||'
            Ligeti > Caplet # Line break to start a new vote. Tags are optionals.
            strangeVote,tag3 || Debussy=Koechlin= Ligeti = Wiltod Lutoslawski = Olivier Messiaen>Caplet * 11 # This vote and his tag will be register 11 times
        ");

        // Creating self Vote object
        $myVote111 = new Vote ( [$myDebussyCandidate, $myLutoCandidate, 'Caplet'], 'customeVoteTag,AnAnotherTag' );
        $myVote112 = new Vote ( 'Olivier Messiaen = Caplet > Wiltod Lutoslawski', ['customVoteTag','AnAnotherTag'] );

        $myElection1->addVote($myVote111);
        $myElection1->addVote($myVote112);


    # -B- Manage Votes

        # 1- Get vote list
            // Get the vote list
            $myElection1->getVotesList(); // Returns an array of all votes as object.

            // How many Vote with tag "strangeVote" ?
            $myElection1->countVotes('strangeVote'); // Return 12 (int)

            // Return this 12 votes !
            $myElection1->getVotesList('strangeVote');
            // Or without this tags and get the first of them
            $oneVoteToDelete = $myElection1->getVotesList('strangeVote', false)[0] ;

        # 2- Vote objet
            var_dump($myVote111->getRanking()); // Return the current ranking
            var_dump($myVote111->getContextualVote($myElection1)); // Return the full ranking in the context of election 1 (with 6 candidates)

            // Change the vote
            $myVote111->setRanking  ( [
                                $myLutoCandidate,
                                $myDebussyCandidate,
                                $myElection1->getCandidateObjectByName('Caplet'),
                                $myElection1->getCandidateObjectByName('Ligeti')]
                                );
                                // Note that when a Vote object is linked to one or more elections, you can can only change his ranking by passing Candidate object.

            // Check the vote history
            $myVote111->getHistory();


        # 3- Delete Votes

            // Delete a specific vote object
            $myElection1->removeVote( $oneVoteToDelete );

            // Delete all vote with tag "strangeVote" or "frenchies"
            $myElection1->removeVote( ['strangeVote','frenchies'] );

            // Delete all vote without tag 'Wagnerian'
            # $myElection1->removeVote( ['strangeVote','frenchies'], false ); // Here, if uncomment, all the vote will be deleted.


// V - Get Result

            // Natural Condorcet Winner
            $myElection1->getWinner(); // Return NULL if there is not, else return the winner candidate object
            $myElection1->getWinner('Schulze'); // Same thing, but try to resolve by Schulze method if there is not one. Can return an array of winners if there are multiple.

            // Natural Condorcet Loser
            $myElection1->getLoser(); // Return NULL if there is not, else return the winner candidate object

            // Advanced Method
            $myElection1->getResult(); // Result set for defaut method (Should be Schulze Winning)
            $myElection1->getResult('Copeland'); // Do it with the Copeland method

            // Get an easy game outcome to read and understand (Table populated by string)
            $easyResult = Condorcet::format($myElection1->getResult(),false);
            // Print it directly (vardump()) :
            # Condorcet::format($myElection1->getResult());


// VI - Play with Condorcet objects (Advanced)

        // Create a second election
        $myElection2 = new Election ();

         // Create three candidate : 'A', 'B' and 'C'
        for ($i = 0 ; $i < 3 ; $i++) {
            $myElection2->addCandidate();
        }


    # Same candidate in multiple elections

        // Add two participating candidates from $myElection1
        $myElection2->addCandidate( $myElection1->getCandidateObjectByName('Debussy') );
        $myElection2->addCandidate( $myLutoCandidate );

        // And, I can change again theirs name. The new name is now applied in the two elections and their votes. If namesake in another election, an exception is throw.
        $myLutoCandidate->setName('W.Lutoslawski');

        // Have a look on $myLutoCandidate history
        $myLutoCandidate->getHistory();

        // In what elections, this candidates have a part ?
            $myLutoCandidate->getLinks(); // Get Condorcet objects
            $myLutoCandidate->countLinks(); // Or just count it


    # The same vote applied to multiple elections.

        $myNewVote = new Vote ( [$myLutoCandidate,'Debussy'] );

        // Add it on election 1 and 2
        $myElection1->addVote( $myNewVote ); // Note that Vote has been altered. 'Debussy' string become a reference to $myElection1->getCandidateObjectByName('Debussy'); Cause there are namesake and there was not any conflict.
        $myElection2->addVote( $myNewVote );
        

        // In what election, this candidates have a part ?
            $myNewVote->getLinks(); // Get Condorcet objects
            $myNewVote->countLinks(); // Or just count it

            // Get the vote ranking in context of each elections
            foreach ($myNewVote->getLinks() as &$link)
            {
                $myNewVote->getContextualVote($link);
            }

        // Now we can change vote ranking

            $myNewVote->setRanking([$myElection1->getCandidateObjectByName('Debussy'),$myLutoCandidate]); // If these votes are already engaged in the election, you must use compatible Candidate objects with all relevant elections. Else, get a nice exception.

            # Get Ranking history
            $myNewVote->getHistory();


print 'Success!  
Process in: '. (microtime(true) - $start_time) . 's';