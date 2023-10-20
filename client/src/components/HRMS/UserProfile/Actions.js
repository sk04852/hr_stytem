import React,{ useEffect, useState } from 'react'
import {
  Button, Input, Label, Modal, ModalBody,
  ModalFooter
} from 'reactstrap'
import ActionData from '../../../data/timelineData'

import './user.css'
function Actions (props) {
  const { fixNavbar } = props
  const [readMore, setReadMore] = useState(false)
  const [comment, setComment] = useState('')
  const readMoreModel = item => {
    setReadMore(true)
    setComment(item)
  }
  const [editProfile, setEditProfile] = useState(false)
  const [jobDetails, setJobDetails] = useState('')
  const [startDate, setStartDate] = useState('')
  const [name, setName] = useState('')
  const [id, setId] = useState(0)
  const [sort, setSort] = useState(0)

  const [actionData, setActionData] = useState([])
  const selectedRecord = e => {
    const id = parseInt(e.target.value)

    setId(id)
    actionData.forEach(item => {
      if (item.Id === id) {
        setJobDetails(item.job_associated)
        setComment(item.comment)
        setStartDate(item.date)
        setName(item.name)
      }
    })
  }
  const editRecord = () => {
    setEditProfile(!editProfile)
  }
  const saveRecord = () => {
    setEditProfile(!editProfile)
    const newState = actionData.map(item => {
      if (item.Id === id) {
        return {
          ...item,
          name: name,
          date: startDate,
          comment: comment,
          job_associated: jobDetails
        }
      }
      return item
    })
    console.log(newState)
    setActionData(newState)
  }
  const updateJob = e => {
    console.log(e.target.value)
    setJobDetails(e.target.value)
  }
  const updateStartDate = e => {
    console.log(e.target.value)
    setStartDate(e.target.value)
  }
  const updateComment = e => {
    console.log(e.target.value)
    setComment(e.target.value)
  }
  const updateName = e => {
    console.log(e.target.value)
    setName(e.target.value)
  }

  useEffect(() => {
    setActionData(ActionData)
  }, [])
  useEffect(() => {
    console.log('updated', actionData)
  }, [sort])
  const sortByDate = () => {
    let sortedData = actionData.sort(function (a, b) {
      var dateA = new Date(a.date),
        dateB = new Date(b.date)
      return dateA - dateB
    })
    console.log(sortedData)
    setSort(1)
    setActionData(sortedData)
  }
  const sortByName = () => {
    let sortedData = actionData.sort((a, b) => (a.name > b.name ? 1 : -1))
    console.log(sortedData)
    setSort(2)
    setActionData(sortedData)
  }
  const sortByJob = () => {
    let sortedData = actionData.sort((a, b) =>
      a.job_associated > b.job_associated ? 1 : -1
    )
    console.log(sortedData)
    setSort(3)
    setActionData(sortedData)
  }

  const handleClose = () => setReadMore(false)
  return (
    <>
      <div class='card'>
        <div class='card-header '>
          <div class='dropdown'>
            <button
              class='btn btn-secondary dropdown-toggle'
              type='button'
              id='dropdownMenuButton'
              data-toggle='dropdown'
              aria-haspopup='true'
              aria-expanded='false'
            >
              New Action
            </button>
            <div class='dropdown-menu' aria-labelledby='dropdownMenuButton'>
              <button class='dropdown-item' onClick={sortByDate}>
                Sort by Date
              </button>
              <button class='dropdown-item'>Sort by Time</button>
              <button class='dropdown-item' onClick={sortByJob}>
                Sort by Job
              </button>
              <button class='dropdown-item' onClick={sortByName}>
                Sort by User Name
              </button>
            </div>
          </div>

          <div class='pointer' onClick={editRecord}>
            <i class='icon-pencil ml-3 '></i>
          </div>
        </div>
        <div class='card-body'>
          {!editProfile ? (
            <div class='container'>
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
                            <th>Type</th>
                            <th>User Name</th>
                            <th>Date</th>
                            <th>Job Associated</th>
                            <th>Comment</th>
                          </tr>
                        </thead>
                        <tbody>
                          {actionData.map(item => (
                            <tr>
                              <td>{item.Id}</td>
                              <td>
                                <img
                                  className='tl_avatar'
                                  src={item.type}
                                  alt='fake_url'
                                  style={{width:"10%"}}
                                />
                              </td>
                              <td>
                                <h6 href=''>{item.name}</h6>
                              </td>
                              <td>
                                <h6 href=''> {item.date}</h6>
                              </td>

                              <td>
                                {' '}
                                <h6 className='font600'>
                                  {item.job_associated}
                                </h6>
                              </td>
                              <td>
                                {' '}
                                <div className='msg'>
                                  <p>
                                    {item.comment.length > 30 ? (
                                      <>
                                        <p> {item.comment.slice(0, 31)}</p>
                                        <div>
                                          <button
                                            onClick={() =>
                                              readMoreModel(item.comment)
                                            }
                                            class='btn btn-primary'
                                          >
                                            Read More
                                          </button>
                                        </div>
                                      </>
                                    ) : (
                                      <p>{item.comment}</p>
                                    )}
                                  </p>
                                </div>
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
          ) : (
            <>
              {id === 0 ? (
                <>
                  <Label for='exampleSelect'>
                    Select User you Want to Update
                  </Label>
                  <Input
                    type='select'
                    name='select'
                    id='exampleSelect'
                    onChange={selectedRecord}
                  >
                    <option>Select User Name</option>
                    {actionData.map(item => (
                      <option value={item.Id}>{item.name}</option>
                    ))}
                  </Input>
                </>
              ) : (
                <>
                  <Label>Date of Action</Label>
                  <Input
                    type='date'
                    name='date_of_action'
                    placeholder='Date of Action'
                    defaultValue={startDate}
                    onChange={updateStartDate}
                  />
                  <Label>Name</Label>
                  <Input
                    type='text'
                    name='name'
                    placeholder='name'
                    defaultValue={name}
                    onChange={updateName}
                  />
                  <Label>Job Assoxiated</Label>
                  <Input
                    type='text'
                    name='job'
                    placeholder='Job Associated '
                    defaultValue={jobDetails}
                    onChange={updateJob}
                  />

                  <Label>Comment</Label>
                  <Input
                    type='text'
                    name='comment'
                    placeholder='Comment'
                    defaultValue={comment}
                    onChange={updateComment}
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
            </>
          )}
        </div>
      </div>{' '}
      <div>
        {readMore ? (
          <Modal isOpen={readMore} toggle={readMoreModel}>
            <ModalBody>{comment} </ModalBody>
            <ModalFooter>
              <Button color='secondary' onClick={() => handleClose()}>
                Cancel
              </Button>
            </ModalFooter>
          </Modal>
        ) : (
          ''
        )}
      </div>
    </>
  )
}

export default Actions
