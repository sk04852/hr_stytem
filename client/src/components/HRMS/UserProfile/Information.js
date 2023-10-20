import React,{ useEffect, useState } from 'react'
import {
  Input, Label
} from 'reactstrap'
import JobData from '../../../data/job'
import Timeline from './Timeline'
import './user.css'
function Information () {
  const [editProfile, setEditProfile] = useState(false)
  const [jobDetails, setJobDetails] = useState('')
  const [startDate, setStartDate] = useState('')
  const [endDate, setEndDate] = useState('')
  const [designation, setDesignation] = useState('')
  const [match, setMatch] = useState(0)
  const [id, setId] = useState(0)

  const [jobData, setJobData] = useState([])
  const selectedRecord = e => {
    const id = parseInt(e.target.value)

    setId(id)
    JobData.forEach(item => {
      if (item.Id === id) {
        console.log(item.company)
        setJobDetails(item.company)
        setDesignation(item.designation)
        setEndDate(item.end_date)
        setStartDate(item.start_date)
        setMatch(item.status)
      }
    })
  }
  const editRecord = () => {
    setEditProfile(!editProfile)
  }
  const saveRecord = () => {
    setEditProfile(!editProfile)
    const newState = jobData.map(item => {
      if (item.Id === id) {
        return {
          ...item,
          company: jobDetails,
          start_date: startDate,
          end_date: endDate,
          status: match,
          designation:designation
        }
      }
      return item;
      
    })
    console.log(newState)
    setJobData(newState)
  }
  const updateJob = e => {
    console.log(e.target.value)
    setJobDetails(e.target.value)
  }
  const updateDesignation = e => {
    console.log(e.target.value)
    setDesignation(e.target.value)
  }
  const updateStartDate = e => {
    console.log(e.target.value)
    setStartDate(e.target.value)
  }
  const updateEndDate = e => {
    console.log(e.target.value)
    setEndDate(e.target.value)
  }
  const updateMatch = e => {
    console.log(e.target.value)
    setMatch(parseInt(e.target.value))
  }
  useEffect(() => {
    setJobData(JobData)
  }, [])
  return (
    <>
      <div class='card'>
        <div class='card-header float-right'>
          <h1>Job History</h1>
          <div class='pointer' onClick={editRecord}>
            <i class='icon-pencil ml-3 '></i>
          </div>
        </div>
        <div class='card-body'>
          {!editProfile ? (
            <div class='container'>
              <Timeline JobData={jobData} />
            </div>
          ) : (
            <div>
              {id === 0 ? (
                <>
                  <Label for='exampleSelect'>
                    Select Job  you Want to Update
                  </Label>
                  <Input
                    type='select'
                    name='select'
                    id='exampleSelect'
                    onChange={selectedRecord}
                  >
                    <option>Select Job</option>
                    {JobData.map(item => (
                      <option value={item.Id}>{item.company}</option>
                    ))}
                  </Input>
                </>
              ) : (
                <>
                  <Label>Company</Label>
                  <Input
                    type='text'
                    name='job'
                    placeholder='Company'
                    defaultValue={jobDetails}
                    onChange={updateJob}
                  />
                    <Label>Designation</Label>
                  <Input
                    type='text'
                    name='job'
                    placeholder='Designation'
                    defaultValue={designation}
                    onChange={updateDesignation}
                  />
                  <Label>Start Date</Label>
                  <Input
                    type='date'
                    name='start_date'
                    placeholder='Start Date'
                    defaultValue={startDate}
                    onChange={updateStartDate}
                  />
                  <Label>End Date</Label>
                  <Input
                    type='date'
                    name='end_date'
                    placeholder='End Date'
                    defaultValue={endDate}
                    onChange={updateEndDate}
                  />

                  <Label>Percentage of Match</Label>
                  <Input
                    type='number'
                    name='match'
                    placeholder='Percentage of Match'
                    defaultValue={match}
                    onChange={updateMatch}
                  />
                  <div>
                    <button
                      class='btn btn-primary float-right mt-3'
                      onClick={saveRecord}
                    >
                      Save
                    </button>
                  </div>
                </>
              )}
            </div>
          )}
        </div>
      </div>{' '}
    </>
  )
}

export default Information
