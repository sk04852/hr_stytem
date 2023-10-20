<?php

use Illuminate\Database\Seeder;
use App\Models\FieldOptionType;
use App\Models\FieldOption;

class FieldOptionTypeSeeder extends Seeder
{

    public function run()
    {
        $fieldOptionsArray = [];
        $fieldOptionsArray["Types of client documents:Document Types"] = "Document Types:General File,CV,Report,Receipt,Invoice,Bill,Other,Unknown";
        $fieldOptionsArray["Status for client documents:Document Status"] = "Client document status:Pending,Approved,Not Approved";
        $fieldOptionsArray["Time Span:Time span to be used search between dates"] = "Time span to be used search between dates:Today,This Week,This Month,This Year";
        $fieldOptionsArray["Lead Filters:Filters used for leads search"] = "Filters used for leads search:New,Pending,Interested,Not Interested,Answered,Not Answered";
        $fieldOptionsArray["Days List:starting_day_of_the_week"] = "Days:Saturday,Sunday,Monday,Tuesday";
        $fieldOptionsArray["Minutes List:popup_reminder_interval "] = "Minutes:1 Minutes,5 Minutes,15 Minutes,30 Minutes,45 Minutes,1 Hour,1 Day";
        $fieldOptionsArray["Date Format:date_format"] = "Format:yyyy-mm-dd ,mm-dd-yyyy ,dd-mm-yyyy";
        $fieldOptionsArray["Calendar Hour Format:calendar_hour_format"] = "Format:yyyy-mm-dd ,mm-dd-yyyy ,dd-mm-yyyy";
        $fieldOptionsArray["Activity Type:default_activity_type"] = "Activity:Call,Meeting,Mobile Call";
        $fieldOptionsArray["Calander View:default_calendar_view"] = "Calander View:Today,This Week,This Month,This Year";
        $fieldOptionsArray["Event Status:default_event_status"] = "Event Status:Planned,Held,Not Held";
        $fieldOptionsArray["Event Duration:other_event_duration_mins"] = "Event Duration:5,10,30,120";
        $fieldOptionsArray["Time Zone:time_zone"] = "Time Zone: (UTC) London Edinburgh Dublin Lisbon ,(UTC-11:00) Samoa";
        $fieldOptionsArray["Hour Formate:calendar_hour_format"] = "Hour Formate: 12,24";
        $fieldOptionsArray["Language:language"] = "Language: US English ,British English,IT Italian,DE Deutsch ,Turkce Dil Paketi";
        $fieldOptionsArray["Currency Decimal:number_of_currency_decimals"] = "Currency Decimal: 0,1,2,3,4,5";
        $fieldOptionsArray["Symbol:symbol_placement"] = "Symbol: $1.0,1.0$";
        $fieldOptionsArray["Days Timing:day_starts_at"] = "Timing: 12:00 AM,1:00 AM ,2:00 AM,3:00 AM,4:00 AM,5:00 AM,6:00 AM,7:00 AM,8:00 AM,9:00 AM,10:00 AM,11:00 AM,12:00 PM,1:00 PM,2:00 PM,3:00 PM ,4:00 PM ,5:00 PM ,6:00 PM ,7:00 PM ,8:00 PM ,9:00 PM ,10:00 PM ,11:00 PM";
        $fieldOptionsArray["Groups Pattern:digit_grouping_pattern"] = "Groups Pattern:123,456,789,123456789,123456,789,12,34,56,789";
        $fieldOptionsArray["Currency:currency"] = "Currency:USA, Dollars,EUR,Euro";
        $fieldOptionsArray["Languages:Languages"] = "Languages:English,Urdu,Spanish,Greek,Russian";
        $fieldOptionsArray["Priorities for tickets:Ticket Priorities"] = "Ticket Priorities:Low,Medium,High,Highest,Urgent";
        $fieldOptionsArray["Status for tickets:Ticket Status"] = "Ticket Status:Open,In Progress, Wait For Response,Done,Closed";
        $fieldOptionsArray["Severity for tickets:Ticket Severity"] = "Ticket Severity:Major,Minor,Feature,Complain,Critical";
        $fieldOptionsArray["Types for tickets:Ticket Types"] = "Ticket Types:Sales,Website Support,Mobile Application Support,Customer Support,Other Technical,Bugs";
        $fieldOptionsArray["Category for tickets:Ticket Category"] = "Ticket Category:Big Problem, Small Problem, Other Problem";
        $fieldOptionsArray["Status for leads:Lead Status"] = "Lead Status: New,Not Interested,Interested,Call Back,Call Back Future,Voice Mail,Pending,No Answer,Wrong Number";
        $fieldOptionsArray["Environment for Trading Platform:Environment"] = "Environment:Demo,Live";
        $fieldOptionsArray["Platform for Trading Platform:Platform"] = "Platform:Meta Trader 4, Meta Trader 5, XOH, cTrader, Mobious Trader,WOW Trader";
        $fieldOptionsArray["CRM for Trading Platform:CRM"] = "CRM:fxtrust.fxdirectcrm.com, marketbrokers.fxdirectcrm.com, fxfinest.fxdirectcrm.com, primehandel.fxdirectcrm.com, fxfixed.fxdirectcrm.com, kapitaltrades.fxdirectcrm.com";
        $fieldOptionsArray['trading_status:Account Trading Status'] = "Trading Status:Self Traded, Managed Account";
        $fieldOptionsArray['secondary_income:Account Secondary Income'] = "Account Secondary Income:None, 10000 - 20000,20000 - 50000,50000 – 80000,80000 – 100000,Over 100000*";
        $fieldOptionsArray['secondary_source_of_income:Account Secondary Source Of Income'] = "Account Secondary Source Of Income:Savings, Investments,Sale of Property,Private Pension,Inheritance / Gift,Loan/Borrowings";
        $fieldOptionsArray['anticipated_account_turnover_annually:Account Anticipated Account Turnover Annually'] = "Account Anticipated Account Turnover Annually:Under €5000, €5000-€15000,€15000-€60000,€60000 +";
        $fieldOptionsArray['fund_method_country:Account Fund Method Country'] = "Account Fund Method Country:Afghanistan";
        $fieldOptionsArray['proof_of_residence:Account Proof Of Residence'] = "Account Proof Of Residence:Approved, Not Approved";
        $fieldOptionsArray['proof_of_identity:Account Proof Of Identity'] = "Account Proof Of Identity:Approved, Not Approved";
        $fieldOptionsArray['document_status:Account Document Status'] = "Account Document Status:Approved, Not Approved";
        $fieldOptionsArray['account_type_requested:Account Type Requested'] = "Account Type Requested:Silver, Gold, Platinum, VIP, Mini, Micro, Standard";
        $fieldOptionsArray['website_language:Account Webiste Language'] = "Account Webiste Language:Abkhazian, Afar, Afrikaans, Akan, Albanian, Amharic, Arabic, Aragonese,Armenian, Assamese, Avaric, Avestan, Aymara, Azerbaijani, Bambara, Bashkir, Basque, Belarusian, Bengali, Bihari languages, Bislama, Bokm, Bosnian, Breton, Bulgarian, Burmese, Catalan, Central Khmer, Chamorro, Chechen, Chichewa, Chinese, Chuvash, Cornish, Corsican, Cree, Croatian, Czech, Danish, Maldivian, Dutch, Dzongkha, English, Esperanto, Estonian,Ewe, Faroese, Fijian, Finnish, French, Fulah, Gaelic, Galician, Ganda, Georgian, German, Greek, Guarani, Gujarati, Haitian, Hausa";
        $fieldOptionsArray['account_status:Account Status'] = "Account Status:Deposited, Traded, Interested, Not Interested, Call Back, No Money, Black List, No Answer";
        $fieldOptionsArray['id_type:Account Id Type'] = "Account Id Type:CCPT, NIDN, CONCAT,LEI";
        $fieldOptionsArray['account_stage:Account Stage'] = "Account Stage:Registered, Deposited, Traded";
        $fieldOptionsArray['client_type:Account Client Type'] = "Account Client Type:Individual, Corporate Account, Common Account";
        $fieldOptionsArray['annual_income:Account Annual Income'] = "Account Annual Income:0 - 15k, 30K,50K,+100K,+500K,+1M,+5M";
        $fieldOptionsArray['net_worth:Account Net Worth'] = "Account Net Worth:0 - 15k, 30K,50K,+100K,+500K,+1M,+5M";
        $fieldOptionsArray['purpose_of_investment:Account Purpose Of Investment'] = "Account Purpose Of Investment:Capital Growth, Income,Hedging,Trading Profits";
        $fieldOptionsArray['estimated_deposit_amount:Account Estimated Deposit Amount'] = "Account Estimated Deposit Amount:<5K, 5K - 10K, 20K,50K,100K, 100K-500K, >500K";
        $fieldOptionsArray['risk_reward_profile_investing:Account Risk Reward Profile Investing'] = "Account Risk Reward Profile Investing:Speculation, Medium term growth, Preservation of capital";
        $fieldOptionsArray['referral_channel:Account Refferal Chennal'] = "Account Refferal Chennal:Public Media, Recommendation, Internet,Other";
        $fieldOptionsArray['education_level:Account Education Level'] = "Account Education Level:Secondary,High School, Bachelor Degree, Master Degree,Doctrate";
        $fieldOptionsArray['professional_status:Account Professional Status'] = "Account Professional Status:Employee,Entrepreneur, Employee & Entrepreneur, Unemployed";
        $fieldOptionsArray['profession_or_industry:Account Profession Or Industry'] = "Account Profession Or Industry:Art/Architecture/Design, Automotive / Transportation, Education / Consulting / Training, Finance / Banking / Financial Markets, Food / Retail / Service, Government, Health / Medical, Law / Legal, Media / Entertainment, Technology / Electronics / IT services, Weapon manufacturers, Arms Trade and Defense, Art and antique dealers, Unregulated charities, Construction, Unregulated non-for-profit organizations, Electronic gambling /gaming through the internet, Dealers in high value or precious goods, Remittance houses, Exchange houses, Betting, Gambling related, Bank note traders, Cash intensive business, Pharmaceutical, Adult industry";
        $fieldOptionsArray['field_of_study:Account Field Of Study'] = "Account Field Of Study:Accounting,Finance Or Similar,Computer Science, IT Engineering similar, Medicine and Sciences or similar, Art, Fashion, Languages, Media or similar";
        $fieldOptionsArray['position:Account Position'] = "Account Position:New";
        $fieldOptionsArray['spoken_language:Account Spoken Language'] = "Account Spoken Language:Abkhazian, Afar, Afrikaans, Akan, Albanian, Amharic, Arabic, Aragonese, Armenian, Assamese, Avaric, Avestan, Aymara, Azerbaijani, Bambara, Bashkir, Basque, Belarusian, Bengali, Bihari languages, Bislama, Bokm, Bosnian, Breton, Bulgarian, Burmese, Catalan, Central Khmer, Chamorro, Chechen, Chichewa, Chinese,Chuvash,Cornish, Corsican,Cree, Croatian, Czech, Danish, Maldivian, Dutch, Dzongkha, English, Esperanto,  Estonian, Ewe, Faroese, Fijian, Finnish, French,Fulah, Gaelic, Galician, Ganda, Georgian, German,Greek";
        $fieldOptionsArray['level_of_experience: Account Level Of Experience'] = "Account Level Of Experience:I have limited experience in trading financial instruments,I have no experience in trading financial instruments, I have extensive experience in trading financial instruments";
        $fieldOptionsArray['in_the_past_2_years: Account In The Past 2 Years'] = "Account In The Past 2 Years:Mostly traded on my own,Worked in a related role in the financial industry, Attended relevant seminars and tutorials,Researched and learned about financial markets in my free time,Performed at least two of the above,None of the above";
        $fieldOptionsArray['size_financial_instrument: Account Size Financial Instrument'] = "Account Size Financial Instrument:True, False";
        $fieldOptionsArray['percentage_money_risk_losing: Account Percentage Money Risk Losing'] = "Account Percentage Money Risk Losing:More than 70%,40% - 70%,10% - 40%,None. I don’t want any risk";
        $fieldOptionsArray['experience_question_4: Account Experience Question 4'] = "Account Experience Question 4:5000 USD,10000 USD,15000 USD,20000 USD";
        $fieldOptionsArray['experience_question_5: Account Experience Question 5'] = "Account Experience Question 5:Long,Short";
        $fieldOptionsArray['experience_question_6: Account Experience Question 6'] = "Account Experience Question 6:Buy Stop Order,Stop Loss Order,Take Profit Order,None of the above";
        $fieldOptionsArray['experience_question_7: Account Experience Question 7'] = "Account Experience Question 7:Stay the same,Go up,Go down";
        $fieldOptionsArray['experience_question_8: Account Experience Question 8'] = "Account Experience Question 8:Investing in bonds,Trade which speculates on the falling price of a financial instrument,A position with less than 1 lot,Trade which speculates on the growing price of a financial instrument";
        $fieldOptionsArray['cfds_trading_frequency: Account CFDS Trading Frequency'] = "Account CFDS Trading Frequency:Never,Rarely, Somtimes, Often";
        $fieldOptionsArray['forex_trading_frequency: Account Forex Trading Frequency'] = "Account Forex Trading Frequency:Never,Rarely, Somtimes, Often";
        $fieldOptionsArray['betting_trading_frequency: Account Betting Trading Frequency'] = "Account Betting Trading Frequency:Never,Rarely, Somtimes, Often";
        $fieldOptionsArray['options_trading_frequency: Account Options Trading Frequency'] = "Account Options Trading Frequency:Never,Rarely, Somtimes, Often";
        $fieldOptionsArray['registration_country:Account Registration Country'] = "Account Registration Country:Afghanistan, Unknown, Aland Islands, Albania, Algeria, American Samoa, Andorra, Angola, Anguilla, Antarctica, Antigua and Barbuda, Argentina, Armenia, Aruba, Australia, Austria, Azerbaijan, Bahamas, Bahrain, Bangladesh, Barbados, Belarus, Belgium, Belize, Benin, Bermuda, Bhutan, Bolivia, Bosnia and Herzegovina, Botswana, Bouvet Island, Brazil, British Indian Ocean Territory, Brunei Darussalam, Bulgaria, Burkina Faso, Burundi, Cambodia, Cameroon, Canada, Cape Verde, Cayman Islands, Central African Republic, Chad, Chile, China, Colombia, Comoros, Congo, Cook Islands, Costa Rica, Cote D'Ivoire, Croatia, Cuba, Cyprus, Czech Republic, Denmark, Djibouti, Dominica, Dominican Republic, Ecuador, Egypt, El Salvador, Equatorial Guinea, Eritrea, Estonia, Ethiopia, Falkland Islands (Malvinas), Faroe Islands, Fiji, Finland, France, French Guiana, French Polynesia, Gabon, Gambia, Georgia, Germany, Ghana, Gibraltar, Greece, Greenland, Grenada, Guadeloupe, Guam, Guatemala, Guernsey, Guinea, Guinea-Bissau, Guyana, Haiti, Heard Island and McDonald Islands, Holy See (Vatican City State), Honduras, Hong Kong, Hungary, Iceland, India, Indonesia, Iran, Iraq, Ireland, Isle of Man, Israel, Italy, Jamaica, Japan, Jersey, Jordan, Kazakstan, Kenya, Kiribati, Korea Democratic People'S Republic Of, Korea, Kuwait, Kyrgyzstan, Lao People'S Democratic Republic, Latvia, Lebanon, Lesotho, Liberia, Libyan Arab Jamahiriya, Liechtenstein, Lithuania, Luxembourg, Macau, Macedonia, Madagascar, Malawi, Malaysia, Maldives, Mali, Malta, Marshall Islands, Martinique, Mauritania, Mauritius, Mayotte, Mexico, Micronesia, Moldova, Monaco, Mongolia, Montserrat, Morocco, Mozambique, Myanmar, Namibia, Nauru, Nepal, Netherlands, Netherlands Antilles, New Caledonia, New Zealand, Nicaragua, Niger, Nigeria, Niue, Norfolk Island, Northern Mariana Islands, Norway, Oman, Pakistan, Palau, Palestinian Territory, Occupied, Panama, Papua New Guinea, Paraguay, Peru, Philippines, Poland, Portugal, Puerto Rico, Qatar, Reunion, Romania, Russian Federation, Rwanda, Saint Lucia, Saint Pierre and Miquelon, Saint Vincent and the Grenadines, Samoa, San Marino, Sao Tome and Principe, Saudi Arabia, Senegal, Serbia, Seychelles, Sierra Leone, Singapore, Slovakia, Slovenia, Solomon Islands, Somalia, South Africa, Spain, Sri Lanka, Sudan, Suriname, Svalbard and Jan Mayen, Swaziland, Sweden, Switzerland, Syrian Arab Republic, Taiwan, Tajikistan, Tanzania, Thailand, Togo, Tokelau, Tonga, Trinidad and Tobago, Tunisia, Turkey, Turkmenistan, Turks and Caicos Islands, Tuvalu, Uganda, Ukraine, United Arab Emirates, United Kingdom, United States, United States Minor Outlying Islands, Uruguay, Uzbekistan, Vanuatu, Venezuela, Vietnam, Virgin Islands, British, Virgin Islands, U.S., Wallis and Futuna, Western Sahara, Yemen, Zambia, Zimbabwe";
        $fieldOptionsArray['recover_question: Account Recover Question'] = "Account Recover Question:What is your mother's maiden name?, What is your favorite pet's name?, What is your favorite teacher's name?, What is your favorite TV program?, What is your favorite color?, What is your favorite sports team?";
        $fieldOptionsArray['risk_level: Account Risk Level'] = "Account Risk Level:Low, Average, High";
        $fieldOptionsArray['deposit_country: Account Deposit Country'] = "Account Deposit Country:Afghanistan, Unknown, Aland Islands, Albania, Algeria, American Samoa, Andorra, Angola, Anguilla, Antarctica, Antigua and Barbuda, Argentina, Armenia, Aruba, Australia, Austria, Azerbaijan, Bahamas, Bahrain, Bangladesh, Barbados, Belarus, Belgium, Belize, Benin, Bermuda, Bhutan, Bolivia, Bosnia and Herzegovina, Botswana, Bouvet Island, Brazil, British Indian Ocean Territory, Brunei Darussalam, Bulgaria, Burkina Faso, Burundi, Cambodia, Cameroon, Canada, Cape Verde, Cayman Islands, Central African Republic, Chad, Chile, China, Colombia, Comoros, Congo, Cook Islands, Costa Rica, Cote D'Ivoire, Croatia, Cuba, Cyprus, Czech Republic, Denmark, Djibouti, Dominica, Dominican Republic, Ecuador, Egypt, El Salvador, Equatorial Guinea, Eritrea, Estonia, Ethiopia, Falkland Islands (Malvinas), Faroe Islands, Fiji, Finland, France, French Guiana, French Polynesia, Gabon, Gambia, Georgia, Germany, Ghana, Gibraltar, Greece, Greenland, Grenada, Guadeloupe, Guam, Guatemala, Guernsey, Guinea, Guinea-Bissau, Guyana, Haiti, Heard Island and McDonald Islands, Holy See (Vatican City State), Honduras, Hong Kong, Hungary, Iceland, India, Indonesia, Iran, Iraq, Ireland, Isle of Man, Israel, Italy, Jamaica, Japan, Jersey, Jordan, Kazakstan, Kenya, Kiribati, Korea Democratic People'S Republic Of, Korea, Kuwait, Kyrgyzstan, Lao People'S Democratic Republic, Latvia, Lebanon, Lesotho, Liberia, Libyan Arab Jamahiriya, Liechtenstein, Lithuania, Luxembourg, Macau, Macedonia, Madagascar, Malawi, Malaysia, Maldives, Mali, Malta, Marshall Islands, Martinique, Mauritania, Mauritius, Mayotte, Mexico, Micronesia, Moldova, Monaco, Mongolia, Montserrat, Morocco, Mozambique, Myanmar, Namibia, Nauru, Nepal, Netherlands, Netherlands Antilles, New Caledonia, New Zealand, Nicaragua, Niger, Nigeria, Niue, Norfolk Island, Northern Mariana Islands, Norway, Oman, Pakistan, Palau, Palestinian Territory, Occupied, Panama, Papua New Guinea, Paraguay, Peru, Philippines, Poland, Portugal, Puerto Rico, Qatar, Reunion, Romania, Russian Federation, Rwanda, Saint Lucia, Saint Pierre and Miquelon, Saint Vincent and the Grenadines, Samoa, San Marino, Sao Tome and Principe, Saudi Arabia, Senegal, Serbia, Seychelles, Sierra Leone, Singapore, Slovakia, Slovenia, Solomon Islands, Somalia, South Africa, Spain, Sri Lanka, Sudan, Suriname, Svalbard and Jan Mayen, Swaziland, Sweden, Switzerland, Syrian Arab Republic, Taiwan, Tajikistan, Tanzania, Thailand, Togo, Tokelau, Tonga, Trinidad and Tobago, Tunisia, Turkey, Turkmenistan, Turks and Caicos Islands, Tuvalu, Uganda, Ukraine, United Arab Emirates, United Kingdom, United States, United States Minor Outlying Islands, Uruguay, Uzbekistan, Vanuatu, Venezuela, Vietnam, Virgin Islands, British, Virgin Islands, U.S., Wallis and Futuna, Western Sahara, Yemen, Zambia, Zimbabwe";
        $fieldOptionsArray['country_of_tax_residency:Account Country Of Tax Residency'] = "Account Country Of Tax Residency:Account Deposit Country:Afghanistan, Unknown, Aland Islands, Albania, Algeria, American Samoa, Andorra, Angola, Anguilla, Antarctica, Antigua and Barbuda, Argentina, Armenia, Aruba, Australia, Austria, Azerbaijan, Bahamas, Bahrain, Bangladesh, Barbados, Belarus, Belgium, Belize, Benin, Bermuda, Bhutan, Bolivia, Bosnia and Herzegovina, Botswana, Bouvet Island, Brazil, British Indian Ocean Territory, Brunei Darussalam, Bulgaria, Burkina Faso, Burundi, Cambodia, Cameroon, Canada, Cape Verde, Cayman Islands, Central African Republic, Chad, Chile, China, Colombia, Comoros, Congo, Cook Islands, Costa Rica, Cote D'Ivoire, Croatia, Cuba, Cyprus, Czech Republic, Denmark, Djibouti, Dominica, Dominican Republic, Ecuador, Egypt, El Salvador, Equatorial Guinea, Eritrea, Estonia, Ethiopia, Falkland Islands (Malvinas), Faroe Islands, Fiji, Finland, France, French Guiana, French Polynesia, Gabon, Gambia, Georgia, Germany, Ghana, Gibraltar, Greece, Greenland, Grenada, Guadeloupe, Guam, Guatemala, Guernsey, Guinea, Guinea-Bissau, Guyana, Haiti, Heard Island and McDonald Islands, Holy See (Vatican City State), Honduras, Hong Kong, Hungary, Iceland, India, Indonesia, Iran, Iraq, Ireland, Isle of Man, Israel, Italy, Jamaica, Japan, Jersey, Jordan, Kazakstan, Kenya, Kiribati, Korea Democratic People'S Republic Of, Korea, Kuwait, Kyrgyzstan, Lao People'S Democratic Republic, Latvia, Lebanon, Lesotho, Liberia, Libyan Arab Jamahiriya, Liechtenstein, Lithuania, Luxembourg, Macau, Macedonia, Madagascar, Malawi, Malaysia, Maldives, Mali, Malta, Marshall Islands, Martinique, Mauritania, Mauritius, Mayotte, Mexico, Micronesia, Moldova, Monaco, Mongolia, Montserrat, Morocco, Mozambique, Myanmar, Namibia, Nauru, Nepal, Netherlands, Netherlands Antilles, New Caledonia, New Zealand, Nicaragua, Niger, Nigeria, Niue, Norfolk Island, Northern Mariana Islands, Norway, Oman, Pakistan, Palau, Palestinian Territory, Occupied, Panama, Papua New Guinea, Paraguay, Peru, Philippines, Poland, Portugal, Puerto Rico, Qatar, Reunion, Romania, Russian Federation, Rwanda, Saint Lucia, Saint Pierre and Miquelon, Saint Vincent and the Grenadines, Samoa, San Marino, Sao Tome and Principe, Saudi Arabia, Senegal, Serbia, Seychelles, Sierra Leone, Singapore, Slovakia, Slovenia, Solomon Islands, Somalia, South Africa, Spain, Sri Lanka, Sudan, Suriname, Svalbard and Jan Mayen, Swaziland, Sweden, Switzerland, Syrian Arab Republic, Taiwan, Tajikistan, Tanzania, Thailand, Togo, Tokelau, Tonga, Trinidad and Tobago, Tunisia, Turkey, Turkmenistan, Turks and Caicos Islands, Tuvalu, Uganda, Ukraine, United Arab Emirates, United Kingdom, United States, United States Minor Outlying Islands, Uruguay, Uzbekistan, Vanuatu, Venezuela, Vietnam, Virgin Islands, British, Virgin Islands, U.S., Wallis and Futuna, Western Sahara, Yemen, Zambia, Zimbabwe";
        $fieldOptionsArray['notification_type:Instant Notification Types'] = "Instant Notification Types:Email,Push Notification,Internal";
        $fieldOptionsArray["Financial Transaction Types:Transaction Type"] = "Financial Transaction Types: Deposit, Withdrawal, Credit In, Credit Out";
        $fieldOptionsArray["name_title: Lead Name Title"] = "Lead Name Title: Mr., Ms., Mrs., Dr., Prof.";

        foreach ($fieldOptionsArray as $fieldOptionKey => $fieldOptionsValue) {

            list($comment, $description) = explode(":", $fieldOptionKey);
            $fieldOptionType = new FieldOptionType();
            $fieldOptionType->type_description = trim($description);
            $fieldOptionType->comment = $comment;
            if ($fieldOptionType->save()) {
                list($typeDescription, $types) = explode(":", $fieldOptionsValue);
                $fieldOption = new FieldOption();
                $typesArray = [];
                foreach (explode(",", $types) as $value) {
                    $typesArray[] = [
                        "type_id" => $fieldOptionType->id,
                        "name" => trim($value),
                        "short_name" => "",
                        "description" => $typeDescription,
                        "sort_order" => 0,
                        "created_at" => new DateTime(),
                        "updated_at" => new DateTime()
                    ];
                }
                $fieldOption->insert($typesArray);
            }
        }
    }
}
