# Annotated data presented at LREC 2020

You can find here the annotated data described in the paper **Rigor Mortis:Annotating MWEs with a Gamified Platform** presented at LREC 2020 in Marseille.

The annotation are described in two files:
 * `RigorMortis_game_annotation.txt` with the 504 sentences of the Annotation part
 * `RigorMortis_bonus_annotation.txt` with the 743 sentences of the Bonus Annotation part

The annotations are presented by sentences, separated by a empty line with the format below:

```
# text : Lui et Bonassoli sont férus de science et avides de publicité .
# number of players : 13
# no mwe - 8 players (61.54%)
# 1 : sont férus - 3 players (23.08%)
# 2 : férus de - 2 players (15.38%)
# 3 : avides de - 2 players (15.38%)
1	Lui	_
2	et	_
3	Bonassoli	_
4	sont	_	1
5	férus	_	1;2
6	de	_	2
7	science	_
8	et	_
9	avides	_	3
10	de	_	3
11	publicité	_
12	.	_
```
