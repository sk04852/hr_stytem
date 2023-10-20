import React, { useEffect, useState } from "react";
import { useHistory, useParams } from "react-router-dom";
import { useDispatch, useSelector } from "react-redux";
import { getEmailTemplates } from "../../../../redux/actions/emailTemplateActions";
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
import CKEditor from "react-ckeditor-component";
import { useTranslation } from "react-i18next";
import Wildcards from "./Wildcards";

const ViewEmailTemplate = (props) => {
  const { fixNavbar } = props;
  const { id } = useParams();

  const history = useHistory();
  const dispatch = useDispatch();

  const { t } = useTranslation();

  const [templateDataById, setTemplateDataById] = useState("");

  useEffect(() => {
    const fetchEmailTemplates = async () => {
      try {
        const response = await dispatch(getEmailTemplates(""));
        if (response.payload.data) {
          let templateData = response.payload.data.find((item) => {
            return item.id === parseInt(id);
          });
          setTemplateDataById(templateData);
        }
      } catch (error) {
        throw error;
      }
    };
    fetchEmailTemplates();
  }, []);

  const settingsSelector = useSelector((state) => state.settings);

  return (
    <div className={`section-body ${fixNavbar ? "marginTop" : ""} mt-3`}>
      <Container fluid={true}>
        <Row className="clearfix">
          <Col md={12}>
            <div className={"text-right mb-3 mr-3"}>
              <Button
                type={"button"}
                color={"primary"}
                onClick={() => history.push("/jobportal-settings/3")}
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
          <Col md={8}>
            <Card>
              <CardHeader>
                <CardTitle
                  className={"font-weight-bold"}
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
                  {templateDataById.title} | {templateDataById.template_key}
                </CardTitle>
              </CardHeader>
              <CardBody>
                <fieldset disabled={true}>
                  <CKEditor
                    activeClass="p10"
                    content={templateDataById.body}
                    id={"body"}
                    name={"body"}
                    config={{
                      readOnly: true,
                    }}
                  />
                </fieldset>
              </CardBody>
            </Card>
          </Col>
          <Wildcards
            activeField={""}
            body={templateDataById.body}
            setBody={""}
            disabledCards={true}
          />
        </Row>
      </Container>
    </div>
  );
};

export default ViewEmailTemplate;
