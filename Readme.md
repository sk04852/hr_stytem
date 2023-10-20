# HR System


### Prerequisite

 1. Install Docker in your system 
	 - Window => "**https://docs.docker.com/desktop/install/windows-install/**"
	 - MAC => "**https://docs.docker.com/desktop/install/mac-install/**"
	 - Linux => "**https://docs.docker.com/desktop/install/ubuntu/**"


2. Add bolow line in the ***Host***  file

	    127.0.0.1 hra-dev.com
		127.0.0.1 company.hra-dev.com
		127.0.0.1 candidate.hra-dev.com

	Location of ***Host*** file

	 - Window => "**C:\Windows\System32\drivers\etc**"
	 - MAC => "**/etc/hosts**"
	 - Linux => "**/etc/hosts**"

### Run Application

    docker compose --env-file ./.env up --build

Errors and Solution: 

 - ***ElasticSerach***:  vm.max_map_count error
	 Solution: 
	 Increase the value of vm.max_map_count to 262144 [reference](https://www.elastic.co/guide/en/elasticsearch/reference/7.17/docker.html#_set_vm_max_map_count_to_at_least_262144)
 - 

### Accessing Application URL

 - Frontend "http://localhost:3000"
 - Backend "http://localhost:8000"
 - phpMyAdmin "http://localhost:8080/"
 - ElasticSearch "https://localhost:9200/"
 - Kibana "https://localhost:5601/"

### Shutdown Application

    docker compose down

### Run Commands

	docker exec -it {CONTAINER_NAME} /bin/sh


### After Run Docker Successfully
	- Import Database backup file into phpMyAdmin "http://localhost:8080/"
	- Run Elastic Search Migration `php artisan elastic:migrate`
	- Export Candidate's Data to ElasticSearch `php artisan scout:import "App\Http\Controllers\CandidateCV\Models\CandidateCV"`
	- Export Job's Data to ElasticSearch `php artisan scout:import "App\Http\Controllers\Jobs\Models\JobPr"`
	- View list of indices: `curl -X GET "localhost:9200/_cat/indices?v"`
	- Delete indices: `curl -X DELETE "http://localhost:9200/candidates,jobs"`