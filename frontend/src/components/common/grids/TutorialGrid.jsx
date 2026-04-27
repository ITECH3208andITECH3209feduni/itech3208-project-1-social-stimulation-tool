import { GridItem, Grid } from "@chakra-ui/react";
import TutorialCard from "../cards/TutorialCard";

function TutorialGrid() {
    return (
        <Grid gap={8} templateColumns={"repeat(3, 1fr)"} templateRows={"repeat(2, 1fr)"}>
            <GridItem>
                <TutorialCard />
            </GridItem>
            <GridItem>
                <TutorialCard />
            </GridItem>
            <GridItem>
                <TutorialCard />
            </GridItem>
            <GridItem>
                <TutorialCard />
            </GridItem>
            <GridItem>
                <TutorialCard />
            </GridItem>
            <GridItem>
                <TutorialCard />
            </GridItem>
        </Grid>
    );
}

export default TutorialGrid;
