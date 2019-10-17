<?php
/**
 * FrontEnd routes
 * 
 * @since 1.0
 *
 * @author Prolitus Dev Team
 */
Route::domain(config('proin.frontend_uri'))->group(function () {
    
    Route::group(['prefix'=>'scout'],function(){
        
    //Registration step 2
    Route::get('education-details',
        [
        'as' => 'scout_education_details',
        'uses' => 'Application\ScoutController@showEducationForm'
    ]);

    Route::post('education-details',
        [
        'as' => 'scout_education_details',
        'uses' => 'Application\ScoutController@saveEducationDetails'
    ]);
    
    //Registration step 3
    Route::get('skills',
        [
        'as' => 'scout_skills',
        'uses' => 'Application\ScoutController@showSkillForm'
    ]);

    Route::post('skills',
        [
        'as' => 'scout_skills',
        'uses' => 'Application\ScoutController@saveSkills'
    ]);
    //Registration step 4
    Route::get('research-publication',
        [
        'as' => 'scout_research_publication',
        'uses' => 'Application\ScoutController@showResearchForm'
    ]);

    Route::post('research-publication',
        [
        'as' => 'scout_reasearch_publication',
        'uses' => 'Application\ScoutController@saveReasearchDetails'
    ]);
    //Registration step 5
    Route::get('awards',
        [
        'as' => 'scout_awards_honors',
        'uses' => 'Application\ScoutController@showAwardForm'
    ]);

    Route::post('awards',
        [
        'as' => 'scout_awards_honors',
        'uses' => 'Application\ScoutController@saveAwardsDetails'
    ]);

    
    });//end of prifix
    
});
