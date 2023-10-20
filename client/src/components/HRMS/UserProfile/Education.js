import React,{ useEffect, useState } from 'react'
import {
  Input, Label
} from 'reactstrap'
import EducationData from '../../../data/education'
import Timeline from './EducationTimeline'
import './user.css'
function Education () {
  const [editProfile, setEditProfile] = useState(false)
  const [jobDetails, setJobDetails] = useState('')
  const [startDate, setStartDate] = useState('')
  const [endDate, setEndDate] = useState('')
  const [degree, setDegree] = useState('')
  const [percentage, setPercentage] = useState(0)
  const [id, setId] = useState(0)

  const [educationData, setEducationData] = useState([])
  const selectedRecord = e => {
    const id = parseInt(e.target.value)

    setId(id)
    EducationData.forEach(item => {
      if (item.Id === id) {
        console.log(item.company)
        setDegree(item.degree)
        setJobDetails(item.school)
        setEndDate(item.end_date)
        setStartDate(item.start_date)
        setPercentage(item.percentage)
      }
    })
  }
  const editRecord = () => {
    setEditProfile(!editProfile)
  }
  const saveRecord = () => {
    setEditProfile(!editProfile)
    const newState = educationData.map(item => {
      if (item.Id === id) {
        return {
          ...item,
          school: jobDetails,
          degree:degree,
          start_date: startDate,
          end_date: endDate,
          percentage: percentage
        }
      }
      return item;
      
    })
    console.log(newState)
    setEducationData(newState)
  }
  const updateJob = e => {
    console.log(e.target.value)
    setJobDetails(e.target.value)
  }
  const updateDegree = e => {
    console.log(e.target.value)
    setDegree(e.target.value)
  }
  const updateStartDate = e => {
    console.log(e.target.value)
    setStartDate(e.target.value)
  }
  const updateEndDate = e => {
    console.log(e.target.value)
    setEndDate(e.target.value)
  }
  const updatepercentage = e => {
    console.log(e.target.value)
    setPercentage(parseInt(e.target.value))
  }
  useEffect(() => {
    setEducationData(EducationData)
  }, [])
  return (
    <>
      <div class='card'>
        <div class='card-header float-right'>
          <h1>Education History</h1>
          <div class='pointer' onClick={editRecord}>
            <i class='icon-pencil ml-3 '></i>
          </div>
        </div>
        <div class='card-body'>
          {!editProfile ? (
            <div class='container'>
              <Timeline EducationData={educationData} />
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
                    <option>Select Education</option>
                    {EducationData.map(item => (
                      <option value={item.Id}>{item.degree}</option>
                    ))}
                  </Input>
                </>
              ) : (
                <>
                  <Label> Degree</Label>
                  <Input
                    type='text'
                    name='job'
                    placeholder='Job Details'
                    defaultValue={degree}
                    onChange={updateDegree}
                  />
                  <Label> School/College/University</Label>
                  <Input
                    type='text'
                    name='job'
                    placeholder='Job Details'
                    defaultValue={jobDetails}
                    onChange={updateJob}
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

                  <Label>Percentage of Marks</Label>
                  <Input
                    type='number'
                    name='percentage'
                    placeholder='Percentage of percentage'
                    defaultValue={percentage}
                    onChange={updatepercentage}
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

export default Education
