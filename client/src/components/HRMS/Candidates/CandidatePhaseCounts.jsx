import React from "react";
import { Row, Col, Card, CardBody } from "reactstrap";
import CountUp from "react-countup";
import { DataLoader } from "../../constants/loaders";

const CandidatePhaseCounts = ({
  phases,
  setCandidateListId,
  setStatusCountNumber,
  setCustomListBgId,
}) => {
  const candidatesFilter = (phaseId) => {
    // it will reset the candidate list
    setCandidateListId("");
    setCustomListBgId("");

    let allCandidateCard = document.getElementById("candidate-phase-cards-1");
    let generalTabCard = document.getElementById("candidate-phase-cards-2");
    let shortlistedCard = document.getElementById("candidate-phase-cards-3");
    let firstInterviewCard = document.getElementById("candidate-phase-cards-4");
    let secondInterviewCard = document.getElementById(
      "candidate-phase-cards-5"
    );
    let offerCard = document.getElementById("candidate-phase-cards-6");
    let placedCard = document.getElementById("candidate-phase-cards-7");
    // let renewalsCard = document.getElementById("candidate-phase-cards-8");

    if (phaseId === 1) {
      // CARD BACKGROUND STYLE START
      allCandidateCard.classList.add("active-tab");
      generalTabCard.classList.remove("active-tab");
      shortlistedCard.classList.remove("active-tab");
      firstInterviewCard.classList.remove("active-tab");
      secondInterviewCard.classList.remove("active-tab");
      offerCard.classList.remove("active-tab");
      placedCard.classList.remove("active-tab");
      // renewalsCard.classList.remove("active-tab");
      // CARD BACKGROUND STYLE END

      setStatusCountNumber(null);
    } else if (phaseId === 2) {
      // CARD BACKGROUND STYLE START
      allCandidateCard.classList.remove("active-tab");
      generalTabCard.classList.add("active-tab");
      shortlistedCard.classList.remove("active-tab");
      firstInterviewCard.classList.remove("active-tab");
      secondInterviewCard.classList.remove("active-tab");
      offerCard.classList.remove("active-tab");
      placedCard.classList.remove("active-tab");
      // renewalsCard.classList.remove("active-tab");
      // CARD BACKGROUND STYLE END

      setStatusCountNumber(2);
    } else if (phaseId === 3) {
      // CARD BACKGROUND STYLE START
      allCandidateCard.classList.remove("active-tab");
      generalTabCard.classList.remove("active-tab");
      shortlistedCard.classList.add("active-tab");
      firstInterviewCard.classList.remove("active-tab");
      secondInterviewCard.classList.remove("active-tab");
      offerCard.classList.remove("active-tab");
      placedCard.classList.remove("active-tab");
      // renewalsCard.classList.remove("active-tab");
      // CARD BACKGROUND STYLE END

      setStatusCountNumber(3);
    } else if (phaseId === 4) {
      // CARD BACKGROUND STYLE START
      allCandidateCard.classList.remove("active-tab");
      generalTabCard.classList.remove("active-tab");
      shortlistedCard.classList.remove("active-tab");
      firstInterviewCard.classList.add("active-tab");
      secondInterviewCard.classList.remove("active-tab");
      offerCard.classList.remove("active-tab");
      placedCard.classList.remove("active-tab");
      // renewalsCard.classList.remove("active-tab");
      // CARD BACKGROUND STYLE END

      setStatusCountNumber(4);
    } else if (phaseId === 5) {
      // CARD BACKGROUND STYLE START
      allCandidateCard.classList.remove("active-tab");
      generalTabCard.classList.remove("active-tab");
      shortlistedCard.classList.remove("active-tab");
      firstInterviewCard.classList.remove("active-tab");
      secondInterviewCard.classList.add("active-tab");
      offerCard.classList.remove("active-tab");
      placedCard.classList.remove("active-tab");
      // renewalsCard.classList.remove("active-tab");
      // CARD BACKGROUND STYLE END

      setStatusCountNumber(5);
    } else if (phaseId === 6) {
      // CARD BACKGROUND STYLE START
      allCandidateCard.classList.remove("active-tab");
      generalTabCard.classList.remove("active-tab");
      shortlistedCard.classList.remove("active-tab");
      firstInterviewCard.classList.remove("active-tab");
      secondInterviewCard.classList.remove("active-tab");
      offerCard.classList.add("active-tab");
      placedCard.classList.remove("active-tab");
      // renewalsCard.classList.remove("active-tab");
      // CARD BACKGROUND STYLE END

      setStatusCountNumber(6);
    } else if (phaseId === 7) {
      // CARD BACKGROUND STYLE START
      allCandidateCard.classList.remove("active-tab");
      generalTabCard.classList.remove("active-tab");
      shortlistedCard.classList.remove("active-tab");
      firstInterviewCard.classList.remove("active-tab");
      secondInterviewCard.classList.remove("active-tab");
      offerCard.classList.remove("active-tab");
      placedCard.classList.add("active-tab");
      // renewalsCard.classList.remove("active-tab");
      // CARD BACKGROUND STYLE END

      setStatusCountNumber(7);
    }
    // else if (phaseId === 8) {
    //   // CARD BACKGROUND STYLE START
    //   allCandidateCard.classList.remove("active-tab");
    //   generalTabCard.classList.remove("active-tab");
    //   shortlistedCard.classList.remove("active-tab");
    //   firstInterviewCard.classList.remove("active-tab");
    //   secondInterviewCard.classList.remove("active-tab");
    //   offerCard.classList.remove("active-tab");
    //   placedCard.classList.remove("active-tab");
    //   renewalsCard.classList.add("active-tab");
    //   // CARD BACKGROUND STYLE END

    //   setStatusCountNumber(8);
    // }
  };

  return (
    <Row>
      {phases && phases.length > 0 ? (
        phases.map((phase, index) => (
          <Col lg={3} md={6} key={index}>
            <Card
              id={`candidate-phase-cards-${phase.id}`}
              onClick={() => candidatesFilter(phase.id)}
            >
              <CardBody className="w_sparkline hover_set">
                <div className="details">
                  <span>{phase.name}</span>
                  <h3 className="mb-0">
                    <CountUp end={phase["counts"]} />
                  </h3>
                </div>
                <div className="w_chart">
                  <div id="mini-bar-chart1" className="mini-bar-chart" />
                </div>
              </CardBody>
            </Card>
          </Col>
        ))
      ) : (
        <DataLoader />
      )}
    </Row>
  );
};

export default CandidatePhaseCounts;
