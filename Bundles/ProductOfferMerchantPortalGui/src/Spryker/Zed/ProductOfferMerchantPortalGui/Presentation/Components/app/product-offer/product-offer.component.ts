import { ChangeDetectionStrategy, Component, Input, ViewEncapsulation } from '@angular/core';
import { TableConfig } from '@spryker/table';
import { ToJson } from "@spryker/utils";

@Component({
    selector: 'mp-product-offer',
    templateUrl: './product-offer.component.html',
    styleUrls: ['./product-offer.component.less'],
    changeDetection: ChangeDetectionStrategy.OnPush,
    encapsulation: ViewEncapsulation.None
})
export class ProductOfferComponent {
    @Input() @ToJson() tableConfig: TableConfig;
    @Input() title: string;
}
