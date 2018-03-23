SELECT *  FROM `annotations` WHERE `sentence_id` = 19795  
ORDER BY `annotations`.`word_position` ASC


SELECT count(*) as count , sentence_id, relation_id, word_position  FROM `annotations` join on relations on relations.id = annotations.relation_id where relations.type = 'trouverTete' and playable=1 and corpus_id in (52,53) group by sentence_id, relation_id, word_position having count >= 2;



create table duplicate_annotations 
select annotations.* from annotations join (
SELECT count(*) as count , sentence_id, relation_id, governor_position  FROM `annotations` 
join relations on relations.id = annotations.relation_id 
where relations.type = 'trouverDependant' and  playable=1  and source_id = 3 and corpus_id in (52,53) 
group by sentence_id, relation_id, governor_position having count >= 2) duplicate 
on duplicate.sentence_id = annotations.sentence_id and duplicate.relation_id = annotations.relation_id 
and duplicate.governor_position = annotations.governor_position 
where annotations.source_id = 3 group by sentence_id, relation_id, governor_position having max(id);

insert into duplicate_annotations 
select annotations.* from annotations join (
SELECT count(*) as count , sentence_id, relation_id, word_position  FROM `annotations` 
join relations on relations.id = annotations.relation_id 
where relations.type = 'trouverTete' and  playable=1  and source_id = 3 and corpus_id in (52,53) 
group by sentence_id, relation_id, word_position having count >= 2) duplicate 
on duplicate.sentence_id = annotations.sentence_id and duplicate.relation_id = annotations.relation_id 
and duplicate.word_position = annotations.word_position  
where annotations.source_id = 3 group by sentence_id, relation_id, word_position having max(id);


update annotations, duplicate_annotations set annotations.playable = 0 where annotations.id = duplicate_annotations.id;