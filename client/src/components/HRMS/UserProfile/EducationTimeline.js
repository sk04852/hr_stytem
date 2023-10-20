import { Timeline, TimelineEvent } from 'react-event-timeline'
import './user.css'
import React from 'react'
function TimeLine (props) {
  return (
    <>
      <Timeline>
        {props.EducationData.map(item => {
          const date = `${item.start_date} - ${item.end_date}`
          return (
            <TimelineEvent
              title={item.degree}
              createdAt={date}
              icon={<i class='fas fa-dot-circle'></i>}
            >
              <p>{item.school}</p>
              <p>
                Marks : {item.percentage}
                {'% '}{' '}
              </p>
            </TimelineEvent>
          )
        })}
      </Timeline>
    </>
  )
}

export default TimeLine
