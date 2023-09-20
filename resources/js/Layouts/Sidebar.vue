
<script setup>
    import { Link } from '@inertiajs/vue3';
    import SideNavLink from '@/Pages/CustomComponents/Layout/SideNavLink.vue';
    import SideNavDropDown from '@/Pages/CustomComponents/Layout/SideNavDropDown.vue';
    import SideDropDownItemLink from '@/Pages/CustomComponents/Layout/SideDropDownItemLink.vue';
    import ProjectLogo from '@/Pages/CustomComponents/Others/ProjectLogo.vue';
    import globalMixin from '../globalMixin';
    const { isLinkActive,canAny} = globalMixin();

</script>
<template>
    <!-- sidebar -->
    <aside id="drawer-navigation" class="iw-sidebar fixed inset-y-0 flex-wrap items-center justify-between block p-0 my-4 overflow-y-auto overflow-x-hidden antialiased transition-transform duration-200 -translate-x-full bg-white border-0 shadow-xl dark:shadow-none dark:bg-slate-850 max-w-[220px] ease z-999 xl:ml-6 rounded-2xl xl:left-0 xl:translate-x-0">
        <ProjectLogo :href="$page.props.base_url +'/dashboard'">
        </ProjectLogo>
        <hr class="h-px mt-0 mb-4 opacity-25 border-0 bg-transparent bg-gradient-to-r from-transparent via-black/40 to-transparent dark:bg-gradient-to-r dark:from-transparent dark:via-white dark:to-transparent">
        <div class="items-center block w-auto max-h-screen iw-sidenav grow basis-full ps">
            <ul class="flex flex-col pl-0 mb-0" >
                <li class="mt-0.5 w-full">
                    <SideNavLink :href="$page.props.base_url +'/dashboard'" icon="fa fa-tv" color-class="text-indigo-500" data-drawer-hide="drawer-navigation">
                        Dashboard
                    </SideNavLink>
                </li>
                <li class="mt-0.5 w-full" v-if="canAny($page.props.granted_permissions,['term-conditions','cities','states', 'countries', 'companies','branches','ifscs','oil-packings','loose-packings','parameters','branch-prefixes',  'brands', 'party-categories','categories-brands','brands-categories', 'rate-differences','packing-formulas','item-freights','fix-rates'])" >
                    <SideNavDropDown id-name="masters"  icon="fa fa-list" color-class="text-orange-500" :active="isLinkActive(['cities','states', 'countries', 'companies','loose-packings','oil-packings','branches','ifscs','parameters','branch-prefixes',  'brands', 'party-categories','brands-categories','categories-brands', 'rate-differences','packing-formulas','item-freights','fix-rates','branch-bank-accounts','bags','kanda'])">
                        Masters
                    </SideNavDropDown>
                    <ul id="masters" class="py-2 space-y-2 bg-primary/5"
                        v-bind:class="{'hidden':!isLinkActive(['cities','states','districts','countries','companies','branches','banks','ifscs','loose-packings','oil-packings','parameters','branch-prefixes',
                        'brands', 'party-categories','brands-categories','categories-brands', 'rate-differences','packing-formulas', 'item-freights','fix-rates','branch-bank-accounts','bags'
                        ,'term-conditions','kanda'
                    ])}">
                        <SideDropDownItemLink v-if="canAny($page.props.granted_permissions,['cities'])"  :href="$page.props.base_url +'/cities'" :active="isLinkActive(['cities'])"  data-drawer-hide="drawer-navigation">
                            Cities
                        </SideDropDownItemLink>
                        <SideDropDownItemLink v-if="canAny($page.props.granted_permissions,['districts'])"  :href="$page.props.base_url +'/districts'" :active="isLinkActive(['districts'])"  data-drawer-hide="drawer-navigation">
                            Districts
                        </SideDropDownItemLink>
                         <SideDropDownItemLink v-if="canAny($page.props.granted_permissions,['states'])"  :href="$page.props.base_url +'/states'"  :active="isLinkActive(['states'])" data-drawer-hide="drawer-navigation">
                            States
                        </SideDropDownItemLink>
                         <SideDropDownItemLink v-if="canAny($page.props.granted_permissions,['countries'])"  :href="$page.props.base_url +'/countries'"  :active="isLinkActive(['countries'])" data-drawer-hide="drawer-navigation">
                            Countries
                        </SideDropDownItemLink>
                        <SideDropDownItemLink v-if="canAny($page.props.granted_permissions,['companies'])"  :href="$page.props.base_url +'/companies'"  :active="isLinkActive(['companies','branches','branch-bank-accounts'])" data-drawer-hide="drawer-navigation">
                            Companies
                        </SideDropDownItemLink>
                        <SideDropDownItemLink  v-if="canAny($page.props.granted_permissions,['banks'])"  :href="$page.props.base_url +'/banks'"  :active="isLinkActive(['banks'])" data-drawer-hide="drawer-navigation">
                            Banks
                        </SideDropDownItemLink>
                        <SideDropDownItemLink  v-if="canAny($page.props.granted_permissions,['ifscs'])"  :href="$page.props.base_url +'/ifscs'"  :active="isLinkActive(['ifscs'])" data-drawer-hide="drawer-navigation">
                            IFSC
                        </SideDropDownItemLink>
                         <SideDropDownItemLink  v-if="canAny($page.props.granted_permissions,['oil-packings'])"   :href="$page.props.base_url +'/oil-packings'"  :active="isLinkActive(['oil-packings'])">
                            Oil Packings
                        </SideDropDownItemLink>
                        <SideDropDownItemLink  v-if="canAny($page.props.granted_permissions,['loose-packings'])"   :href="$page.props.base_url +'/loose-packings'"  :active="isLinkActive(['loose-packings'])">
                            Loose Packings
                        </SideDropDownItemLink>
                        <SideDropDownItemLink  v-if="canAny($page.props.granted_permissions,['liquor-packings'])"   :href="$page.props.base_url +'/liquor-packings'"  :active="isLinkActive(['liquor-packings'])">
                            Liquor Packings
                        </SideDropDownItemLink>
                           <SideDropDownItemLink v-if="canAny($page.props.granted_permissions,['parameters'])"   :href="$page.props.base_url +'/parameters'"  :active="isLinkActive(['parameters'])">
                            Parameters
                        </SideDropDownItemLink>
                        <SideDropDownItemLink  v-if="canAny($page.props.granted_permissions,['branch-prefixes'])"    :href="$page.props.base_url +'/branch-prefixes'"  :active="isLinkActive(['branch-prefixes'])">
                            Branch Prefixes
                        </SideDropDownItemLink>
                         <SideDropDownItemLink  v-if="canAny($page.props.granted_permissions,['brands'])"    :href="$page.props.base_url +'/brands'"  :active="isLinkActive(['brands','brands-categories'])">
                            Brands
                        </SideDropDownItemLink>
                        <SideDropDownItemLink  v-if="canAny($page.props.granted_permissions,['party-categories'])"    :href="$page.props.base_url +'/party-categories'"  :active="isLinkActive(['party-categories','categories-brands'])">
                            Party Categories
                        </SideDropDownItemLink>
                          <SideDropDownItemLink v-if="canAny($page.props.granted_permissions,['rate-differences'])" :href="$page.props.base_url +'/rate-differences'"   :active="isLinkActive(['rate-differences'])" data-drawer-hide="drawer-navigation">
                            Ltr Tin Difference
                        </SideDropDownItemLink>
                        <SideDropDownItemLink v-if="canAny($page.props.granted_permissions,['packing-formulas'])" :href="$page.props.base_url +'/packing-formulas'"   :active="isLinkActive(['packing-formulas'])" data-drawer-hide="drawer-navigation">
                            Packing Formulas
                        </SideDropDownItemLink>
                        <SideDropDownItemLink v-if="canAny($page.props.granted_permissions,['item-freights'])" :href="$page.props.base_url +'/item-freights'"   :active="isLinkActive(['item-freights'])" data-drawer-hide="drawer-navigation">
                            Item Freights
                        </SideDropDownItemLink>
                        <SideDropDownItemLink v-if="canAny($page.props.granted_permissions,['fix-rates'])" :href="$page.props.base_url +'/fix-rates'"   :active="isLinkActive(['fix-rates'])" data-drawer-hide="drawer-navigation">
                            Fix Rates
                        </SideDropDownItemLink>
                        <SideDropDownItemLink v-if="canAny($page.props.granted_permissions,['term-conditions'])" :href="$page.props.base_url +'/term-conditions'"   :active="isLinkActive(['term-conditions'])" data-drawer-hide="drawer-navigation">
                            Terms Conditions
                        </SideDropDownItemLink>
                        <SideDropDownItemLink v-if="canAny($page.props.granted_permissions,['tds-section'])" :href="$page.props.base_url +'/tds-section'"   :active="isLinkActive(['tds-section'])" data-drawer-hide="drawer-navigation">
                            TDS Section
                        </SideDropDownItemLink>
                        <SideDropDownItemLink v-if="canAny($page.props.granted_permissions,['bags'])" :href="$page.props.base_url +'/bags'"   :active="isLinkActive(['bags'])" data-drawer-hide="drawer-navigation">
                            Bags
                        </SideDropDownItemLink>
                        <SideDropDownItemLink v-if="canAny($page.props.granted_permissions,['kanda'])" :href="$page.props.base_url +'/kanda'"   :active="isLinkActive(['kanda'])" data-drawer-hide="drawer-navigation">
                            Kanda
                        </SideDropDownItemLink>

                        <SideDropDownItemLink v-if="canAny($page.props.granted_permissions,['cheque-setting'])" :href="$page.props.base_url +'/cheque-setting'"   :active="isLinkActive(['cheque-setting'])" data-drawer-hide="drawer-navigation">
                            Cheque Setting
                        </SideDropDownItemLink>
                    </ul>
                </li>
                <li class="mt-0.5 w-full" v-if="canAny($page.props.granted_permissions,['accounts','party-sub-groups','clients','vendors','pay-terms','opening','other-accounts','gl-sub-groups','limit-sub-groups'])">
                    <SideNavDropDown id-name="accounts" icon="fa fa-user" color-class="text-purple-500" :active="isLinkActive(['accounts','party-sub-groups','clients','vendors','pay-terms','opening','other-accounts','gl-sub-groups','limit-sub-groups'])">
                        Accounts
                    </SideNavDropDown>
                    <ul id="accounts" class="py-2 space-y-2 bg-primary/5" v-bind:class="{'hidden':!isLinkActive(['accounts','party-sub-groups','clients','vendors','pay-terms','opening','other-accounts','gl-sub-groups','limit-sub-groups'])}">
                        <SideDropDownItemLink v-if="canAny($page.props.granted_permissions,['accounts'])" :href="$page.props.base_url +'/accounts'"  :active="isLinkActive(['accounts','opening'])"  data-drawer-hide="drawer-navigation">
                            Accounts
                        </SideDropDownItemLink>
                         <SideDropDownItemLink v-if="canAny($page.props.granted_permissions,['other-accounts'])" :href="$page.props.base_url +'/other-accounts'"  :active="isLinkActive(['other-accounts'])"  data-drawer-hide="drawer-navigation">
                            GL Accounts
                        </SideDropDownItemLink>
                         <SideDropDownItemLink v-if="canAny($page.props.granted_permissions,['party-sub-groups'])" :href="$page.props.base_url +'/party-sub-groups'"   :active="isLinkActive(['party-sub-groups'])" data-drawer-hide="drawer-navigation">
                            Party Sub Groups
                        </SideDropDownItemLink>
                         <SideDropDownItemLink v-if="canAny($page.props.granted_permissions,['gl-sub-groups'])" :href="$page.props.base_url +'/gl-sub-groups'"   :active="isLinkActive(['gl-sub-groups'])" data-drawer-hide="drawer-navigation">
                            GL Sub Groups
                        </SideDropDownItemLink>
                         <SideDropDownItemLink v-if="canAny($page.props.granted_permissions,['limit-sub-groups'])" :href="$page.props.base_url +'/limit-sub-groups'"   :active="isLinkActive(['limit-sub-groups'])" data-drawer-hide="drawer-navigation">
                            Limit Sub Groups
                        </SideDropDownItemLink>
                          <SideDropDownItemLink v-if="canAny($page.props.granted_permissions,['clients'])" :href="$page.props.base_url +'/clients'"   :active="isLinkActive(['clients'])" data-drawer-hide="drawer-navigation">
                           Clients
                        </SideDropDownItemLink>
                         <SideDropDownItemLink v-if="canAny($page.props.granted_permissions,['vendors'])" :href="$page.props.base_url +'/vendors'"   :active="isLinkActive(['vendors'])" data-drawer-hide="drawer-navigation">
                           Vendors
                        </SideDropDownItemLink>
                          <SideDropDownItemLink v-if="canAny($page.props.granted_permissions,['pay-terms'])" :href="$page.props.base_url +'/pay-terms'"  :active="isLinkActive(['pay-terms'])"  data-drawer-hide="drawer-navigation">
                           Pay Terms
                        </SideDropDownItemLink>

                    </ul>
                </li>
                <li class="mt-0.5 w-full" v-if="canAny($page.props.granted_permissions,['items','main-groups','sub-groups','other-sub-groups','item-units','stores','gsts','vat-cst'])">
                    <SideNavDropDown id-name="items" icon="fa fa-list-alt" color-class="text-Emerald-700" :active="isLinkActive(['items','main-groups','sub-groups','other-sub-groups','item-units','stores','gsts','vat-cst'])">
                        Items
                    </SideNavDropDown>
                    <ul id="items" class="py-2 space-y-2 bg-primary/5" v-bind:class="{'hidden':!isLinkActive(['items','main-groups','sub-groups','other-sub-groups','item-units','stores','gsts','vat-cst'])}">
                        <SideDropDownItemLink v-if="canAny($page.props.granted_permissions,['items'])" :href="$page.props.base_url +'/items'"  :active="isLinkActive(['items'])"  data-drawer-hide="drawer-navigation">
                            Items
                        </SideDropDownItemLink>
                         <SideDropDownItemLink v-if="canAny($page.props.granted_permissions,['main-groups'])" :href="$page.props.base_url +'/main-groups'"  :active="isLinkActive(['main-groups'])"  data-drawer-hide="drawer-navigation">
                            Main Groups
                        </SideDropDownItemLink>
                         <SideDropDownItemLink v-if="canAny($page.props.granted_permissions,['sub-groups'])" :href="$page.props.base_url +'/sub-groups'"   :active="isLinkActive(['sub-groups'])" data-drawer-hide="drawer-navigation">
                            Sub Groups
                        </SideDropDownItemLink>
                          <SideDropDownItemLink v-if="canAny($page.props.granted_permissions,['other-sub-groups'])" :href="$page.props.base_url +'/other-sub-groups'"   :active="isLinkActive(['other-sub-groups'])" data-drawer-hide="drawer-navigation">
                           Other Sub Groups
                        </SideDropDownItemLink>
                         <SideDropDownItemLink v-if="canAny($page.props.granted_permissions,['item-units'])" :href="$page.props.base_url +'/item-units'"   :active="isLinkActive(['item-units'])" data-drawer-hide="drawer-navigation">
                           Item Units
                        </SideDropDownItemLink>
                        <SideDropDownItemLink v-if="canAny($page.props.granted_permissions,['stores'])" :href="$page.props.base_url +'/stores'"   :active="isLinkActive(['stores'])" data-drawer-hide="drawer-navigation">
                           Stores
                        </SideDropDownItemLink>
                        <SideDropDownItemLink v-if="canAny($page.props.granted_permissions,['gsts'])" :href="$page.props.base_url +'/gsts'"   :active="isLinkActive(['gsts'])" data-drawer-hide="drawer-navigation">
                           GST
                        </SideDropDownItemLink>
                        <SideDropDownItemLink v-if="canAny($page.props.granted_permissions,['vat-cst'])" :href="$page.props.base_url +'/vat-cst'"   :active="isLinkActive(['vat-cst'])" data-drawer-hide="drawer-navigation">
                           VAT/CST
                        </SideDropDownItemLink>

                    </ul>
                </li>

                <li class="mt-0.5 w-full" v-if="canAny($page.props.granted_permissions,[ 'receipt-vouchers', 'payment-vouchers', 'journal-vouchers', 'interest-vouchers', 'transfer-vouchers','vouchers-bill-detail' ])">
                    <SideNavDropDown id-name="vouchers" icon="fa fa-indian-rupee-sign" color-class="text-pink-800" :active="isLinkActive(['vouchers'])">
                        Transactions
                    </SideNavDropDown>
                    <ul id="vouchers" class="py-2 space-y-2 bg-primary/5" v-bind:class="{'hidden':!isLinkActive(['vouchers'])}">
                        <SideDropDownItemLink v-if="canAny($page.props.granted_permissions,['receipt-vouchers', 'payment-vouchers', 'journal-vouchers', 'interest-vouchers', 'transfer-vouchers'])" :href="$page.props.base_url +'/vouchers'"  :active="isLinkActive(['vouchers'])"  data-drawer-hide="drawer-navigation">
                            Vouchers
                        </SideDropDownItemLink>

                        <SideDropDownItemLink v-if="canAny($page.props.granted_permissions,['vouchers-bill-detail'])" :href="$page.props.base_url +'/vouchers-details'"  :active="isLinkActive(['vouchers-details'])"  data-drawer-hide="drawer-navigation">
                           Bill Wise Ref Details
                        </SideDropDownItemLink>
                    </ul>
                </li>

                <li class="mt-0.5 w-full" v-if="canAny($page.props.granted_permissions,[ 'credit-note', 'debit-note'])">
                    <SideNavDropDown id-name="docs" icon="fa fa-indian-rupee-sign" color-class="text-pink-800" :active="isLinkActive(['debit-note','credit-note'])">
                        Gst Documents
                    </SideNavDropDown>
                    <ul id="docs" class="py-2 space-y-2 bg-primary/5" v-bind:class="{'hidden':!isLinkActive(['debit-note','credit-note'])}">
                        <SideDropDownItemLink v-if="canAny($page.props.granted_permissions,['debit-note'])" :href="$page.props.base_url +'/debit-note'"  :active="isLinkActive(['debit-note'])"  data-drawer-hide="drawer-navigation">
                            Debit Note
                        </SideDropDownItemLink>
                        <SideDropDownItemLink v-if="canAny($page.props.granted_permissions,['credit-note'])" :href="$page.props.base_url +'/credit-note'"  :active="isLinkActive(['credit-note'])"  data-drawer-hide="drawer-navigation">
                            Credit Note
                        </SideDropDownItemLink>
                    </ul>

                </li>

                <li class="mt-0.5 w-full" v-if="canAny($page.props.granted_permissions,[ 'issue', 'receipt'])">
                    <SideNavDropDown id-name="trans" icon="fa fa-indian-rupee-sign" color-class="text-pink-800" :active="isLinkActive(['issue','receipt'])">
                       Transfers
                    </SideNavDropDown>
                    <ul id="trans" class="py-2 space-y-2 bg-primary/5" v-bind:class="{'hidden':!isLinkActive(['issue','receipt'])}">
                        <SideDropDownItemLink v-if="canAny($page.props.granted_permissions,['issue'])" :href="$page.props.base_url +'/issue'"  :active="isLinkActive(['issue'])"  data-drawer-hide="drawer-navigation">
                            Issue
                        </SideDropDownItemLink>
                        <SideDropDownItemLink v-if="canAny($page.props.granted_permissions,['receipt'])" :href="$page.props.base_url +'/receipt'"  :active="isLinkActive(['receipt'])"  data-drawer-hide="drawer-navigation">
                            Receipt
                        </SideDropDownItemLink>
                    </ul>

                </li>

                <li class="mt-0.5 w-full" v-if="canAny($page.props.granted_permissions,[ 'purchase-order'])">
                    <SideNavDropDown id-name="purchase" icon="fa fa-indian-rupee-sign" color-class="text-pink-800" :active="isLinkActive(['vouchers'])">
                        Purchase
                    </SideNavDropDown>
                    <ul id="purchase" class="py-2 space-y-2 bg-primary/5" v-bind:class="{'hidden':!isLinkActive(['purchase-orders'])}">
                        <SideDropDownItemLink  v-if="canAny($page.props.granted_permissions,['purchase-order'])"  :href="$page.props.base_url +'/purchase-orders'"  :active="isLinkActive(['purchase-orders'])"  data-drawer-hide="drawer-navigation">
                            Purchase Orders
                        </SideDropDownItemLink>
                    </ul>
                </li>

                <li class="mt-0.5 w-full" v-if="canAny($page.props.granted_permissions,['sale-orders','sale-contracts','sale-order-dispatches','sale-invoices',

                ])">
                    <SideNavDropDown id-name="sales" icon="fa fa-arrow-up-right-dots" color-class="text-pink-800" :active="isLinkActive(['sale-orders','sale-contracts','sale-order-dispatch','sale-invoices'])">
                        Sales
                    </SideNavDropDown>
                    <ul id="sales" class="py-2 space-y-2 bg-primary/5" v-bind:class="{'hidden':!isLinkActive(['sale-orders','sale-contracts','sale-order-dispatch','sale-invoices'])}">
                        <SideDropDownItemLink v-if="canAny($page.props.granted_permissions,['sale-contracts'])" :href="$page.props.base_url +'/sale-contracts'"  :active="isLinkActive(['sale-contracts'])"  data-drawer-hide="drawer-navigation">
                            Sale Contracts
                        </SideDropDownItemLink>
                        <SideDropDownItemLink v-if="canAny($page.props.granted_permissions,['sale-orders'])" :href="$page.props.base_url +'/sale-orders'"  :active="isLinkActive(['sale-orders'])"  data-drawer-hide="drawer-navigation">
                            Sale Orders
                        </SideDropDownItemLink>
                         <SideDropDownItemLink v-if="canAny($page.props.granted_permissions,['sale-order-dispatches'])" :href="$page.props.base_url +'/sale-order-dispatch'"  :active="isLinkActive(['sale-order-dispatch'])"  data-drawer-hide="drawer-navigation">
                            Sale Order Dispatches
                        </SideDropDownItemLink>
                        <SideDropDownItemLink v-if="canAny($page.props.granted_permissions,['sale-invoices'])" :href="$page.props.base_url +'/sale-invoices'"  :active="isLinkActive(['sale-invoices'])"  data-drawer-hide="drawer-navigation">
                            Sale Invoices
                        </SideDropDownItemLink>
                    </ul>
                </li>
                <li class="mt-0.5 w-full" v-if="canAny($page.props.granted_permissions,['sale-report','ledger-report','contract-booking-report' ,'contract-execution-report','sale-format-report','trial-balance-report'])">
                    <SideNavDropDown id-name="reports" icon="fa fa-chart-simple" color-class="text-teal-800" :active="isLinkActive(['sale-report','ledger-report','trial-balance-report','contract-booking-report','sale-format-report'])">
                        Reports
                    </SideNavDropDown>
                    <ul id="reports" class="py-2 space-y-2 bg-primary/5" v-bind:class="{'hidden':!isLinkActive(['sale-report','ledger-report','trial-balance-report','contract-booking-report','contract-execution-report','sale-format-report'])}">
                        <SideDropDownItemLink v-if="canAny($page.props.granted_permissions,['ledger-report','trial-balance-report'])" :href="$page.props.base_url +'/ledger-report'"  :active="isLinkActive(['ledger-report'])"  data-drawer-hide="drawer-navigation">
                            Accounting Reports
                        </SideDropDownItemLink>
                        <SideDropDownItemLink v-if="canAny($page.props.granted_permissions,['sale-report','contract-booking-report' ,'contract-execution-report','sale-format-report'])" :href="$page.props.base_url +'/sale-report'"  :active="isLinkActive(['sale-report','contract-booking-report' ,'contract-execution-report','sale-format-report'])"  data-drawer-hide="drawer-navigation">
                            Sale Reports
                        </SideDropDownItemLink>
                    </ul>
                </li>
                <li class="mt-0.5 w-full"  v-if="canAny($page.props.granted_permissions,['users','roles'])">
                    <SideNavDropDown id-name="users" icon="fa fa-users" color-class="text-red-500" :active="isLinkActive(['users','roles','permissions'])">
                        Users & Roles
                    </SideNavDropDown>
                    <ul id="users" class="py-2 space-y-2 bg-primary/5" v-bind:class="{'hidden':!isLinkActive(['users','roles','permissions'])}">
                       <SideDropDownItemLink v-if="canAny($page.props.granted_permissions,['users'])"   :href="$page.props.base_url +'/users'"  :active="isLinkActive(['users'])"   >
                            Users
                        </SideDropDownItemLink>
                         <SideDropDownItemLink  v-if="canAny($page.props.granted_permissions,['roles'])"   :href="$page.props.base_url +'/roles'"  :active="isLinkActive(['roles'])"   >
                            Roles
                        </SideDropDownItemLink>
                    </ul>
                </li>
            </ul>
        </div>
    </aside>
    <!-- sidebar end  -->
</template>
<style>

</style>
