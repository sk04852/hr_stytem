import { Timeline, TimelineEvent } from "react-event-timeline";
import "../candidate.css";
import React from "react";

const JobTimeline = (props) => {
  return (
    <Timeline>
      {props.JobData.map((item) => {
        const date = `${item.start_date} - ${item.end_date}`;
        return (
          <TimelineEvent
            title={item.company}
            createdAt={date}
            icon={<i className="fas fa-dot-circle"></i>}
          >
            {item.designation}
          </TimelineEvent>
        );
      })}
    </Timeline>
  );
};

export default JobTimeline;
