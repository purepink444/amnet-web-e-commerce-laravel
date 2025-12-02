/**
 * Address Selector Utility
 * Handles province, district, and subdistrict selection for registration forms
 */
export class AddressSelector {
    constructor(options = {}) {
        this.provinceSelect = options.provinceSelect || document.getElementById('province');
        this.districtSelect = options.districtSelect || document.getElementById('district');
        this.subdistrictSelect = options.subdistrictSelect || document.getElementById('subdistrict');
        this.zipcodeInput = options.zipcodeInput || document.getElementById('zipcode');

        this.provinces = [];
        this.districts = [];
        this.subdistricts = [];

        this.init();
    }

    async init() {
        if (this.provinceSelect && this.districtSelect && this.subdistrictSelect) {
            await this.loadProvinces();
            this.bindEvents();
        }
    }

    async loadProvinces() {
        try {
            const response = await fetch('/json/src/provinces.json');
            const data = await response.json();
            this.provinces = data;

            // Populate province dropdown
            data.forEach(province => {
                const option = document.createElement('option');
                option.value = province.provinceCode;
                option.textContent = province.provinceNameTh;
                this.provinceSelect.appendChild(option);
            });
        } catch (error) {
            console.error('Error loading provinces:', error);
        }
    }

    bindEvents() {
        this.provinceSelect.addEventListener('change', () => this.onProvinceChange());
        this.districtSelect.addEventListener('change', () => this.onDistrictChange());
        this.subdistrictSelect.addEventListener('change', () => this.onSubdistrictChange());
    }

    async onProvinceChange() {
        const provinceCode = this.provinceSelect.value;

        // Reset dependent dropdowns
        this.districtSelect.innerHTML = '<option value="">เลือกอำเภอ</option>';
        this.subdistrictSelect.innerHTML = '<option value="">เลือกตำบล</option>';
        this.districtSelect.disabled = !provinceCode;
        this.subdistrictSelect.disabled = true;
        this.zipcodeInput.value = '';

        if (provinceCode) {
            try {
                const response = await fetch('/json/src/districts.json');
                const data = await response.json();
                this.districts = data.filter(d => d.provinceCode == provinceCode);

                this.districts.forEach(district => {
                    const option = document.createElement('option');
                    option.value = district.districtCode;
                    option.textContent = district.districtNameTh;
                    this.districtSelect.appendChild(option);
                });
            } catch (error) {
                console.error('Error loading districts:', error);
            }
        }
    }

    async onDistrictChange() {
        const districtCode = this.districtSelect.value;

        this.subdistrictSelect.innerHTML = '<option value="">เลือกตำบล</option>';
        this.subdistrictSelect.disabled = !districtCode;
        this.zipcodeInput.value = '';

        if (districtCode) {
            try {
                const response = await fetch('/json/src/subdistricts.json');
                const data = await response.json();
                this.subdistricts = data.filter(s => s.districtCode == districtCode);

                this.subdistricts.forEach(subdistrict => {
                    const option = document.createElement('option');
                    option.value = subdistrict.subdistrictNameTh;
                    option.dataset.zipcode = subdistrict.postalCode;
                    option.textContent = subdistrict.subdistrictNameTh;
                    this.subdistrictSelect.appendChild(option);
                });
            } catch (error) {
                console.error('Error loading subdistricts:', error);
            }
        }
    }

    onSubdistrictChange() {
        const selectedOption = this.subdistrictSelect.options[this.subdistrictSelect.selectedIndex];
        this.zipcodeInput.value = selectedOption.dataset.zipcode || '';
    }
}

// Auto-initialize on registration page
document.addEventListener('DOMContentLoaded', () => {
    if (document.getElementById('province')) {
        window.addressSelector = new AddressSelector();
    }
});