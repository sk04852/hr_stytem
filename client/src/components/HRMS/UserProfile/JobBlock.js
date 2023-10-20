import React,{ useEffect, useState } from 'react'
import {
  Input, Label
} from 'reactstrap'
import JobBlockData from '../../../data/jobBlockData'
import './user.css'
function JobBlock () {
  const [editProfile, setEditProfile] = useState(false)

  const [designation, setDesignation] = useState('')
  const [status, setStatus] = useState('')
  
  const [match, setMatch] = useState(0)
  const [id, setId] = useState(0)

  const [jobData, setJobData] = useState([])
  const selectedRecord = e => {
    const id = parseInt(e.target.value)

    setId(id)
    JobBlockData.forEach(item => {
      if (item.Id === id) {
        console.log(item.company)

        setDesignation(item.job_name)

        setMatch(item.match)
        setStatus(item.status)
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
       
          match: match,
          job_name: designation,
          status:status
        }
      }
      return item
    })
    console.log(newState)
    setJobData(newState)
  }
  const updateDesignation = e => {
    console.log(e.target.value)
    setDesignation(e.target.value)
  }
  const updateMatch = e => {
    console.log(e.target.value)
    setMatch(parseInt(e.target.value))
  }
  const updateStatus= e => {
    console.log(e.target.value)
    setStatus((e.target.value))
  }
  useEffect(() => {
    setJobData(JobBlockData)
  }, [])
  return (
    <>
      <div class='card'>
        <div class='card-header float-right'>
          <h1>Jobs</h1>
          <div class='pointer' onClick={editRecord}>
            <i class='icon-pencil ml-3 '></i>
          </div>
        </div>
        <div class='card-body'>
          {!editProfile ? (
            <div class='container'>
              <div className='section-body'>
                <div className='container-fluid'>
                  <div className='tab-content taskboard'>
                    <div
                      className='tab-pane fade show active'
                      id='TaskBoard-list'
                      role='tabpanel'
                    >
                      <div className='table-responsive'>
                        <table className='table table-hover table-vcenter mb-0 table_custom spacing8 text-nowrap'>
                          <thead>
                            <tr>
                              <th>#</th>
                              <th>Job Name</th>
                              <th>Match</th>
                              <th>Status</th>

                            
                            </tr>
                          </thead>
                          <tbody>
                            {jobData.map(item => (
                              <tr>
                                <td>{item.Id}</td>
                                <td>
                                  <h6 className='mb-0'>{item.job_name}</h6>
                                </td>

                                <td>
                                  <div className='clearfix'>
                                    <div className='float-left'>
                                      <strong>{item.match}%</strong>
                                    </div>
                                    <div className='float-right'>
                                      <small className='text-muted'>
                                        Progress
                                      </small>
                                    </div>
                                  </div>
                                  <div className='progress progress-xs'>
                                    <div
                                      className={
                                        item.match < 50
                                          ? 'progress-bar pg-red'
                                          : item.match < 70
                                          ? 'progress-bar pg-yellow'
                                          : 'progress-bar pg-green'
                                      }
                                      role='progressbar'
                                      style={{
                                        width: `${item.match}%`,

                                      }}
                                      aria-valuenow={42}
                                      aria-valuemin={0}
                                      aria-valuemax={100}
                                    />
                                  </div>
                                </td>
                      
                                <td>
                                  <h6 className='mb-0'>{item.status}</h6>
                                </td>
                              </tr>
                            ))}
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          ) : (
            <div>
              {id === 0 ? (
                <>
                  <Label for='exampleSelect'>
                    Select Job you Want to Update
                  </Label>
                  <Input
                    type='select'
                    name='select'
                    id='exampleSelect'
                    onChange={selectedRecord}
                  >
                    <option>Select Job</option>
                    {JobBlockData.map(item => (
                      <option value={item.Id}>{item.job_name}</option>
                    ))}
                  </Input>
                </>
              ) : (
                <>
                
                  <Label>Job Name</Label>
                  <Input
                    type='text'
                    name='job'
                    placeholder='Designation'
                    defaultValue={designation}
                    onChange={updateDesignation}
                  />
                  
                  <Label>Percentage of Match</Label>
                  <Input
                    type='number'
                    name='match'
                    placeholder='Percentage of Match'
                    defaultValue={match}
                    onChange={updateMatch}
                  />
                  <Label>Status</Label>
                  <Input
                    type='text'
                    name='status'
                    placeholder='Status'
                    defaultValue={status}
                    onChange={updateStatus}
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

export default JobBlock
