import React from "react";
import {
  Button,
  Card,
  CardBody,
  CardHeader,
  CardTitle,
  Col,
  Container,
  Row,
} from "reactstrap";
import { NavLink, useHistory, useParams } from "react-router-dom";
import EmailEditor from "./EmailEditor";
import { useSelector } from "react-redux";
import { useTranslation } from "react-i18next";

const CompanySendingEmails = (props) => {
  const { fixNavbar } = props;
  const { id } = useParams();
  const history = useHistory();

  const { t } = useTranslation();

  const settingsSelector = useSelector((state) => state.settings);

  return (
    <>
      <div className={`section-body ${fixNavbar ? "marginTop" : ""} mt-3`}>
        <Container fluid={true}>
          <Row className="clearfix">
            <Col md={12}>
              <div className={"text-right mb-3 mr-3"}>
                <Button
                  type={"button"}
                  color={"primary"}
                  onClick={() => history.push(`/hr-companies/view/${id}`)}
                  style={{
                    fontSize: `${
                      settingsSelector.FontSize === "Large"
                        ? "large"
                        : settingsSelector.FontSize === "Extra Large"
                        ? "x-large"
                        : "14px"
                    }`,
                  }}
                >
                  {t("buttons.back")}
                </Button>
              </div>
            </Col>
            <Col md={12}>
              <div className="container-fluid">
                <ul className="nav nav-tabs page-header-tab">
                  <li className="nav-item">
                    <NavLink
                      to="#"
                      className="nav-link active"
                      style={{
                        fontSize: `${
                          settingsSelector.FontSize === "Large"
                            ? "large"
                            : settingsSelector.FontSize === "Extra Large"
                            ? "x-large"
                            : "15px"
                        }`,
                      }}
                    >
                      {t("sendEmailsTabs.candidateTab")}
                    </NavLink>
                  </li>
                </ul>
              </div>
              <Card>
                <CardHeader>
                  {/*<CardTitle className={"font-weight-bold"}>*/}
                  {/*  CONTENT | KUTSE TOOVESTLUSELE*/}
                  {/*</CardTitle>*/}
                </CardHeader>
                <CardBody>
                  <div className="summernote">
                    <EmailEditor />
                  </div>
                </CardBody>
              </Card>
            </Col>
          </Row>
        </Container>
      </div>
    </>
  );
};

export default CompanySendingEmails;
