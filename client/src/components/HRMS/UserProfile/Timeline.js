import { Timeline, TimelineEvent } from 'react-event-timeline'
import './user.css'
import React from 'react'
function TimeLine (props) {
  return (
    <>
      <Timeline>
        {props.JobData.map(item => {
          const date = `${item.start_date} - ${item.end_date}`
          return (
            <TimelineEvent
              title={item.company}
              createdAt={date}
              icon={<i class='fas fa-dot-circle'></i>}
            >
              {item.designation}
            </TimelineEvent>
          )
        })}
      </Timeline>
    </>
  )
}
//
export default TimeLine
